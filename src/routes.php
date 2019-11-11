<?php

// get all todos
    
    // Retrieve todo with id 
    $app->get('/todo/[{id}]', function ($request, $response, $args) {
         $sth = $this->db->prepare("SELECT * FROM tasks WHERE id=:id");
        $sth->bindParam("id", $args['id']);
        $sth->execute();
        $todos = $sth->fetchObject();
        return $this->response->withJson($todos);
    });
 
 
    // Search for todo with given search teram in their name
    $app->get('/todos/search/[{query}]', function ($request, $response, $args) {
         $sth = $this->db->prepare("SELECT * FROM tasks WHERE UPPER(task) LIKE :query ORDER BY task");
        $query = "%".$args['query']."%";
        $sth->bindParam("query", $query);
        $sth->execute();
        $todos = $sth->fetchAll();
        return $this->response->withJson($todos);
    });
 
    // Add a new todo
    $app->post('/todo', function ($request, $response) {
        $input = $request->getParsedBody();
        $sql = "INSERT INTO tasks (task,status) VALUES (:task,:status)";
         $sth = $this->db->prepare($sql);
        $sth->bindParam("task", $input['task']);
        $sth->bindParam("status", $input['status']);
        $sth->execute();
        $input['id'] = $this->db->lastInsertId();
        return $this->response->withJson($input);
    });
        
 
    // DELETE a todo with given id
    $app->delete('/todo/[{id}]', function ($request, $response, $args) {
         $sth = $this->db->prepare("DELETE FROM tasks WHERE id=:id");
        $sth->bindParam("id", $args['id']);
        $sth->execute();
        $todos = $sth->fetchAll();
        return $this->response->withJson($todos);
    });
 
    // Update todo with given id
    $app->put('/todo/[{id}]', function ($request, $response, $args) {
        $input = $request->getParsedBody();
        $sql = "UPDATE `tasks` SET task=:task,status=:status WHERE id=:id";
         $sth = $this->db->prepare($sql);
        $sth->bindParam("id", $args['id']);
        $sth->bindParam("task", $input['task']);
        $sth->bindParam("status", $input['status']);
        $sth->execute();
        $input['id'] = $args['id'];
        return $this->response->withJson($input);
    });

    $app->options('/{routes:.+}', function ($request, $response, $args) {
        return $response;
    });


    // apartment
        $app->get('/apartment',function ($request, $response, $args){
            $sth = $this->db->prepare("SELECT category.category_name,room.room_id,room.room_name,
                                            room.rooms_address,room.room_price,room.room_facilities,room.room_tell,
                                           room.room_category,room.title_img
                                             FROM room,category
                                                WHERE room.category_id=category.category_id   
                                             AND category_name = 'อพาร์ทเม้นท์' 
                                            ");

        $sth->execute();
        $rooms = $sth->fetchAll();
        return $this->response->withJson($rooms); 
        });   

        //dorm
        $app->get('/dorm',function ($request, $response, $args){
            $sth = $this->db->prepare("SELECT   category.category_name,room.room_name,room.room_id,
                                                room.rooms_address,room.room_price,room.room_facilities,room.room_tell,   
                                               room.room_category,room.title_img 
                                        FROM room,category
                                         WHERE room.category_id=category.category_id
                                         And category_name = 'หอพัก'
                                            ");
                                         
        $sth->execute();
        $rooms = $sth->fetchAll();
        return $this->response->withJson($rooms); 
    });     

    //condo
    $app->get('/condo',function ($request, $response, $args){
        $sth = $this->db->prepare("SELECT   category.category_name,room.room_name,room.room_id,
                                            room.rooms_address,room.room_price,room.room_facilities,room.room_tell,   
                                           room.room_category,room.title_img 
                                    FROM room,category
                                     WHERE room.category_id=category.category_id
                                     And category_name = 'คอนโนมิเนียม'
                                        ");
                                     
    $sth->execute();
    $rooms = $sth->fetchAll();
    return $this->response->withJson($rooms); 
});     

//mamsion
$app->get('/mansion',function ($request, $response, $args){
    $sth = $this->db->prepare("SELECT   category.category_name,room.room_name,room.room_id,
                                        room.rooms_address,room.room_price,room.room_facilities,room.room_tell,   
                                       room.room_category,room.title_img 
                                FROM room,category
                                 WHERE room.category_id=category.category_id
                                 And category_name = 'แมนชัน'
                                    ");                      
$sth->execute();
$rooms = $sth->fetchAll();
return $this->response->withJson($rooms); 
});     




        //deteil
            $app->get('/deteil/[{category_name}={room_id}]',function ($request, $response, $args){
                $sqlimg = $this->db->prepare("SELECT room.room_name, picture.pic_id,
                                                picture.pic_name     
                                                    FROM room,picture
                                             where picture.room_id = room.room_id
                                            and room.room_id = :room_id
                                            ");
                $sqlimg->bindParam("room_id", $args['room_id']);                       
                $sqlimg->execute();
                $img = $sqlimg->fetchAll();
                
                $sqldata = $this->db->prepare(" SELECT category.category_name,room.room_id,room.room_name,room.rooms_address,
                                                        room.room_price,room.room_facilities,room.room_tell,room.room_category
                                                 FROM room,category
                                            where room.category_id = category.category_id
                                            and category_name=:category_name
                                            and room.room_id = :room_id
                                             ");
                 $sqldata->bindParam("room_id", $args['room_id']); 
                 $sqldata->bindParam("category_name", $args['category_name']);                                       
                $sqldata->execute();
                $rooms = $sqldata->fetchAll();
                $body= array('rooms'=>$rooms)+['img'=>$img];
                 return $this->response->withJson($body); 
            });  

            //search
            $app->get('/search/room/[{room_name}]',function ($request, $response, $args){
                $sth = $this->db->prepare("SELECT   category.category_name,room.room_name,room.room_id,
                                                    room.rooms_address,room.room_price,room.room_facilities,room.room_tell,   
                                                   room.room_category,room.title_img 
                                            FROM room,category
                                             WHERE room.category_id=category.category_id
                                            and room_name LIKE :room_name
                                             "); 

             $query = "%".$args['room_name']."%";
             $sth->bindParam("room_name", $query);
            $sth->execute();
            $rooms = $sth->fetchAll();
            return $this->response->withJson($rooms); 
        });     

        //allroom

        $app->get('/roomlist',function ($request, $response, $args){
            $sth = $this->db->prepare("SELECT   category.category_name,room.room_name,room.room_id,
                                                room.rooms_address,room.room_price,room.room_facilities,room.room_tell,   
                                               room.room_category,room.title_img 
                                        FROM room,category
                                         WHERE room.category_id=category.category_id
                                         ");      
                                        
        $sth->execute();
        $rooms = $sth->fetchAll();
        return $this->response->withJson($rooms); 
    });     

    $app->get('/showroom/[{room_name}]',function ($request, $response, $args){
        $sth = $this->db->prepare("SELECT   category.category_name,room.room_name,room.room_id,
                                            room.rooms_address,room.room_price,room.room_facilities,room.room_tell,   
                                           room.room_category,room.title_img 
                                    FROM room,category
                                     WHERE room.category_id=category.category_id
                                     and room_name = :room_name
                                     ");      
             $sth->bindParam("room_name", $args['room_name']);                       
    $sth->execute();
    $rooms = $sth->fetchAll();
    return $this->response->withJson($rooms); 
});     

    //searchprice
    $app->get('/room/price20',function ($request, $response, $args){
        $sth = $this->db->prepare("SELECT   category.category_name,room.room_name,room.room_id,
                                            room.rooms_address,room.room_price,room.room_facilities,room.room_tell,   
                                           room.room_category,room.title_img 
                                    FROM room,category
                                     WHERE room.category_id=category.category_id
                                    and room_price < 2500 
                                     "); 

    
    $sth->execute();
    $rooms = $sth->fetchAll();
    return $this->response->withJson($rooms); 
});     
$app->get('/room/price25',function ($request, $response, $args){
    $sth = $this->db->prepare("SELECT   category.category_name,room.room_name,room.room_id,
                                        room.rooms_address,room.room_price,room.room_facilities,room.room_tell,   
                                       room.room_category,room.title_img 
                                FROM room,category
                                 WHERE room.category_id=category.category_id
                                and room_price between 2500 and 3500
                                 "); 


$sth->execute();
$rooms = $sth->fetchAll();
return $this->response->withJson($rooms); 
});   

$app->get('/room/price30',function ($request, $response, $args){
    $sth = $this->db->prepare("SELECT category.category_name,room.room_name,room.room_id,
                                        room.rooms_address,room.room_price,room.room_facilities,room.room_tell,   
                                       room.room_category,room.title_img 
                                FROM room,category
                                 WHERE room.category_id=category.category_id
                                and room_price >3500
                                 "); 


$sth->execute();
$rooms = $sth->fetchAll();
return $this->response->withJson($rooms); 
});     

