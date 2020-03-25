<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Factory\AppFactory;
include 'DbHandler.php';
require __DIR__ . '/vendor/autoload.php';

$app = AppFactory::create();
$app->addErrorMiddleware(true, true, true);

$app->setBasePath('/api');

/*$mw = function (Request $request, RequestHandler $handler) {
    $response = $handler->handle($request);
    $response->getBody()->write('World');
    return $response;
};


$app->get('/hello/{name}', function (Request $request, Response $response, array $args) {
    $name = $args['name'];
    $response->getBody()->write("Hello, $name");
    return $response;
});



$app->get('/query', function (Request $request, Response $response, array $args) {
    $params = $request->getQueryParams();
    print_r($params);
    return $response;
});*/

$app->get('/articles', function (Request $request, Response $response, $args) {
	$db = new DbHandler();
	$total_records =$db->totalRow();
	$current_page = isset($_GET['page']) ? $_GET['page'] : 1;
    $limit = 5;
    $total_page = ceil($total_records / $limit);

    // Giới hạn current_page trong khoảng 1 đến total_page
    if ($current_page > $total_page){
        $current_page = $total_page;
    }
    else if ($current_page < 1){
        $current_page = 1;
    }
    $start = ($current_page - 1) * $limit;
    $result=$db->getAll($start,$limit);
    $result = array(
                    'info'        => '0',
                    'totalRecord' => $total_records,
                    'totalPage'   => $total_page,
                    'perPage'     => $limit,
                    'page'        => $current_page,
                    'data'        => $result,
                );
	$payload = json_encode($result);
	$response->getBody()->write($payload);
	return $response
	          ->withHeader('Content-Type', 'application/json');
});

$app->get('/articles/{article}', function (Request $request, Response $response, $args) {
	$id = $args['article'];
	$db = new DbHandler();
	$result = $db->getOne($id);
	$payload = json_encode($result);
	$response->getBody()->write($payload);
	return $response
	          ->withHeader('Content-Type', 'application/json');
});

$app->post('/articles', function ($request, $response, $args) {
	$data = $request->getParsedBody();
	$title=trim($data['title']);
	$data=[
		'title'=>$title,
		'created_date'=>date("Y-m-d H:i:s")
	];
	$db = new DbHandler();
	$idInsert= $db->insertTitle($data);
	$result=['info'=>0,'message'=>'insert success','last_id_insert'=>$idInsert];
	$payload = json_encode($result);
	$response->getBody()->write($payload);
	return $response
	          ->withHeader('Content-Type', 'application/json');
});


$app->put('/articles', function ($request, $response, $args) {
	$paramPut= $request->getBody()->getContents();
	$paramPut=json_decode($paramPut);
	$id=$paramPut->id;
	$title=trim($paramPut->title);
	$dataUpdate=[
		'title'=>$title,
		'updated_date'=>date('Y-m-d H:i:s')
	];
	$db = new DbHandler();
	$idInsert= $db->updateTitle($id,$dataUpdate);
	$result=['info'=>0,'message'=>'update success'];
	$payload = json_encode($result);
	$response->getBody()->write($payload);
	return $response
	          ->withHeader('Content-Type', 'application/json');
});

$app->delete('/articles/{article}', function ($request, $response, $args) {
	$id = $args['article'];
	$db = new DbHandler();
	$db->deleteTitle($id);
	$result=['info'=>0,'message'=>'delete success'];
	$payload = json_encode($result);
	$response->getBody()->write($payload);
	return $response
	          ->withHeader('Content-Type', 'application/json');
});


$app->run();