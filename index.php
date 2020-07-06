<?php
require __DIR__ . '/../vendor/autoload.php';

$app = new \Slim\Slim(array(
    'mode' => 'development'
));

$app->configureMode('production', function() use ($app) {
    $app->config(array(
        'log.enabled' => true,
        'debug' => false
    ));
});

$app->configureMode('development', function() use ($app) {
    $app->config(array(
        'log.enabled' => true,
        'debug' => true
    ));
});

// variables for return values
$states = array('NOT_CHARGING',
    'CHARGING', 'CHARGED', 'TO_DESTINATION',
    'AT_DESTINATION', 'WAITING_FOR_LOCK', 'WAITING_FOR_ELEVATOR',
    'WAITING_FOR_DOOR', 'UNAVAILABLE');
$statuses = array('Pushed on FirstFloor', 'Charging on',
    'Restarted on FirstFloor', 'Charged on Dock 4',
    'Charged On');
$obstacleStates = array("","Contact", "Stop", "Blocked",
    "Clear", "Obstructed", "BehindTug");
$types = array("AREA", "CHARGING", "STATION");
$names = array("2South"=>"2 South", "Dock1"=>"Dock 1", "Dock2351"=>"Dock 2351",
    "Ward_A"=>"Ward_A","T3Drop1"=>"T3Drop1","SpinRight2"=>"SpinRight2", "SpinRight"=>"SpinRight",
    "Point1"=>"Point1", "Pharmacy"=>"Pharmacy", "F01_02P"=>"F01_02P","F01_02D"=>"F01_02D");
$jobTypes = array("USER_REQUESTED", "SCHEDULED", "INTERNAL_API");
$jobStates = array("SCHEDULED", "IN_PROGRESS", "COMPLETED", "CANCELED", "EXPIRED");
// To implement
//TODO: Error responses

//Sample
$app->get('/hello/:name', function ($name) {
echo "Hello, " . $name;
});

// 1) GET /status(/:tid)
$app->get('/status/:tid', function ($tid) use ($app, $states, $statuses, $obstacleStates) {
    $rtn = array();

    $state = $states[rand(0, count($states)-1)];
    $status = $statuses[rand(0, count($statuses)-1)];
    $obstacleState = $obstacleStates[rand(0, count($obstacleStates)-1)];

    $rtn[] = array('tid' => $tid,
        'state' => $state,
        'status' => $status,
        'xCoordinate' => rand(-2000, 2000)/ rand(1, 2000),
        'yCoordinate' => rand(-2000, 2000)/ rand(1, 2000),
        'heading' => rand(-2000, 2000)/ rand(1, 2000),
        'battery' => rand(0, 100),
        'jid' => rand(0, 5),
        'pid' => rand(0, 5),
        'executionState' => rand(0, 9),// or 0
        'errorState' => rand()%2 == 0 ? 0 : pow(2, rand(0, 9)),
        'obstacleState' => $obstacleState,
        'error' => rand(0, 6),
        'liftPosition' => "down", //TODO
        'cartDetected' => "off" , //TODO
        'idleTime' => 11234, //TODO
        'lastCommunicationTime' => 13124, //TODO
        'atDestination' => rand(0, 1),
        'available'=> rand(0, 1)
    );

    $json = json_encode($rtn);
    echo $json;

});

// 2) GET /status
$app->get('/status', function () use ($app, $states, $statuses, $obstacleStates) {
    $rtn = array();
    $num = rand(1, 10);

    for ($i = 0; $i <= $num; $i++) {
        $state = $states[rand(0, count($states) - 1)];
        $status = $statuses[rand(0, count($statuses) - 1)];
        $obstacleState = $obstacleStates[rand(0, count($obstacleStates) - 1)];

        $rtn[] = array('tid' => "Tug-".strval(rand(1, 100)."-".strval(rand(1, 100))),
            'state' => $state,
            'status' => $status,
            'xCoordinate' => rand(-2000, 2000) / rand(1, 2000),
            'yCoordinate' => rand(-2000, 2000) / rand(1, 2000),
            'heading' => rand(-2000, 2000) / rand(1, 2000),
            'battery' => rand(0, 100),
            'jid' => rand(0, 5),
            'pid' => rand(0, 5),
            'executionState' => rand(0, 9),// or 0
            'errorState' => rand() % 2 == 0 ? 0 : pow(2, rand(0, 9)),
            'obstacleState' => $obstacleState,
            'error' => rand(0, 6),
            'liftPosition' => "down", //TODO
            'cartDetected' => "off", //TODO
            'idleTime' => 11234, //TODO
            'lastCommunicationTime' => 13124, //TODO
            'atDestination' => rand(0, 1),
            'available' => rand(0, 1)
        );
    }
    $json = json_encode($rtn);
    echo $json;

});

// 3) GET /destination/
$app->get('/destination/', function () use ($names, $types) {
    $rtn = array();
    $num = rand(1, 10);

    for ($i = 0; $i <= $num; $i++) {
        $names_name = array_rand($names);
        $names_alias = $names[$names_name];

        $rtn[] = array(
            'did' => rand(1, 100),
            'name' => $names_name,
            'alias' => $names_alias,
            'type' => $types[rand(0, 2)],
            'aid' => rand(1, 10),
            'gid' => rand(0, 100),
            'enabled' => rand(0, 1)
        );
    }
    $json = json_encode($rtn);
    echo $json;

});

// 4) GET /job
$app->get('/job', function () use ($jobTypes, $jobStates, $names) {

    $rtn = array();

    $num_i = rand(1, 10);
    for ($i = 0; $i < $num_i; $i++) {

        $tid = "Tug-" . strval(rand(1, 1000)) . "-" . strval(rand(1, 1000));
        $jid = rand(1, 100);

        $timestamp = rand(strtotime("2020-06-01"), strtotime("2020-8-01"));
        $requested_time = date("Y-m-d H:i:s", $timestamp);
        $start_time = date("Y-m-d H:i:s", $timestamp + rand(0, 100));


        $itinerary = array();
        $num_j = rand(1, 5);

        for ($j = 0; $j < $num_j; $j++) {
            $startState = array("cart" => rand(1, 10), "lift" => rand(1, 10), "prox" => rand(1, 10));
            $endState = array("cart" => rand(1, 10), "lift" => rand(1, 10), "prox" => rand(1, 10));

            $itinerary[] = array(
                "rid" => rand(1, 1000000),
                "did" => rand(1, 1000),
                "destination" => array_rand($names),
                "start" => null,
                "startState" => $startState,
                "end" => null,
                "endState" => $endState,
                "timeOut" => rand(0, 10),
                "state" => $jobStates[rand(0, count($jobStates) - 1)]
            );
        }

        $startState = array("cart" => rand(1, 10), "lift" => rand(1, 10), "prox" => rand(1, 10));
        $endState = array("cart" => rand(1, 10), "lift" => rand(1, 10), "prox" => rand(1, 10));


        $rtn[] = array(
            "jid" => $jid,
            "tid" => $tid,
            'pid' => rand(1, 100),
            'pool' => "Pool" . strval(rand(1, 100)),
            'gid' => rand(1, 100),
            'requested' => $requested_time,
            'start' => $start_time,
            'startState' => $startState,
            'end' => null,
            'endState' => $endState,
            'sid' => rand(1, 100),
            'jobType' => $jobTypes[rand(0, count($jobTypes) - 1)],
            'pendingTime' => rand(1, 10),
            'startTime' => rand(1, 1000),
            'state' => $jobStates[rand(0, count($jobStates) - 1)],
            'itinerary' => $itinerary
        );
    }

    $json = json_encode($rtn);
    echo $json;
}) ;

// 5) GET /job(/:id)
$app->get('/job/:id', function ($id) use ($jobTypes, $jobStates, $names) {

    $rtn = array();

    if (stristr($id, 'Tug')) {
        $tid = $id;
        $jid = rand(0, 100);
    }
    else {
        $tid = "Tug-".strval(rand(1, 1000))."-".strval(rand(1, 1000));
        $jid = $id;
    }

    $timestamp = rand( strtotime("2020-06-01"), strtotime("2020-8-01") );
    $requested_time = date("Y-m-d H:i:s", $timestamp);
    $start_time     = date("Y-m-d H:i:s", $timestamp + rand(0, 100));


    $itinerary = array();
    $num_i = rand(1, 5);

    for ($i = 0; $i < $num_i; $i++) {
        $startState = array("cart"=> rand(1, 10), "lift"=>rand(1, 10), "prox"=>rand(1, 10));
        $endState = array("cart"=> rand(1, 10), "lift"=>rand(1, 10), "prox"=>rand(1, 10));

        $itinerary[] = array(
            "rid"=>rand(1, 1000000),
            "did"=>rand(1, 1000),
            "destination"=>array_rand($names),
            "start"=>null,
            "startState"=>$startState,
            "end"=>null,
            "endState"=>$endState,
            "timeOut"=>rand(0, 10),
            "state"=>$jobStates[rand(0, count($jobStates)-1)]
        );
    }

    $startState = array("cart"=> rand(1, 10), "lift"=>rand(1, 10), "prox"=>rand(1, 10));
    $endState = array("cart"=> rand(1, 10), "lift"=>rand(1, 10), "prox"=>rand(1, 10));


    $rtn[] = array(
        "jid" => $jid,
        "tid" => $tid,
        'pid' => rand(1, 100),
        'pool' => "Pool".strval(rand(1, 100)),
        'gid' => rand(1, 100),
        'requested' => $requested_time,
        'start' => $start_time,
        'startState' => $startState,
        'end' => null,
        'endState' => $endState,
        'sid' => rand(1, 100),
        'jobType' => $jobTypes[rand(0, count($jobTypes)-1)],
        'pendingTime' => rand(1, 10),
        'startTime' => rand(1, 1000),
        'state' => $jobStates[rand(0, count($jobStates)-1)],
        'itinerary' => $itinerary
    );

    $json = json_encode($rtn);
    echo $json;
}) ;


// 6) GET /go/:tid
$app->get('/go/:tid', function ($tid) {

    $rtn = array();

    $rtn[] = array(
        "code"=>rand(0,1),
        "tid"=>$tid
    );

    $json = json_encode($rtn);
    echo $json;
});

// 7) POST /pause/:tid
$app->POST('/pause/:tid/', function($tid) {

    $rtn = array();

    $rtn[] = array(
        "code" => rand(0,1),
        "tid" => $tid
    );

    $json = json_encode($rtn);
    echo $json;

});

// 8) POST /continue/:tid
$app->POST('/continue/:tid/', function($tid) {

    $rtn = array();

    $rtn[] = array(
        "code" => rand(0,1),
        "tid" => $tid
    );

    $json = json_encode($rtn);
    echo $json;

});





$app->run();