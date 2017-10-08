<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Firebase\JWT\JWT;
use \Tuupola\Base62;

$app->get('/api/customers/get', function(Request $req,Response $rep){
	$sql = "SELECT * FROM clients";

	try {
		$db = new db;
		$db = $db->connect();
		$query = $db->prepare($sql);
		$query->execute();
		$customers = $query->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo json_encode($customers);
	}catch(PDOException $e){
		echo $e->getMessage();
	}
});

$app->get('/api/customer/get/{id}', function(Request $req,Response $rep){

	$id = $req->getAttribute('id');
	$sql = "SELECT * FROM clients WHERE id = $id";

	try {
		$db = new db;
		$db = $db->connect();
		$query = $db->prepare($sql);
		$query->execute();
		$customer = $query->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo json_encode($customer);
	}catch(PDOException $e){
		echo $e->getMessage();
	}
});

$app->get('/api/customers/compare', function(Request $req,Response $rep){

	$pass 	= $req->getParam('pass');
	$mail 	= $req->getParam('mail');
	
	$sql = "SELECT * FROM clients WHERE mail = :mail AND pass = :pass";

	try {
		$db = new db;
		$db = $db->connect();
		$query = $db->prepare($sql);
		$query->execute([
			":mail" 	=> $mail,
			":pass" 	=> $pass
		]);
		$customer = $query->fetch();
		$db = null;
		if($customer){

			$tokenId    = (new Base62)->encode(random_bytes(16));
			$issuedAt   = time();            //Adding 10 seconds
		    $expire     = $issuedAt + 60;            // Adding 60 seconds

		    $data = [
		        'iat'  => $issuedAt,         // Issued at: time when the token was generated
		        'jti'  => $tokenId,          // Json Token Id: an unique identifier for the token  
		        'exp'  => $expire,           // Expire
		        'data' => [                  // Data related to the signer user
		            'id'   => $customer['id'], // userid from the users table
		            'mail' => $customer['mail'],
		            'nom' => $customer['nom'],
		            'prenom' => $customer['prenom']
		        ]
		    ];

		    $secretKey = base64_decode('w/7JdCjPIqyE2/4jW/ZVBMV8Arv+osvYmB5OVsCXS2rkTh12/v1N7nj1CFZfQafJeWZdgRpxA273tcM9YV/FXw==');

		    $jwt = JWT::encode(
		        $data,      //Data to be encoded in the JWT
		        $secretKey, // The signing key
		        'HS512'     // Algorithm used to sign the token, see https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40#section-3
	        );
	        
		    $unencodedArray = ['jwt' => $jwt];
		    return $rep->withStatus(200)->write(json_encode($unencodedArray));
		}else{
			return $rep->withStatus(401)->write(json_encode("Mauvais mot de passe"));
		}
	}catch(PDOException $e){
		echo $e->getMessage();
	}
});

$app->post('/api/customer/add', function(Request $req,Response $rep){

	$nom 	= $req->getParam('nom');
	$prenom = $req->getParam('prenom');
	$pass 	= $req->getParam('pass');
	$mail 	= $req->getParam('mail');

	$sql 	= "INSERT INTO clients(mail,pass,nom,prenom) VALUES(:mail,:pass,:nom,:prenom)";

	try {
		$db = new db;
		$db = $db->connect();
		$query = $db->prepare($sql);
		$query->execute([
			":mail" 	=> $mail,
			":pass" 	=> $pass,
			":nom" 		=> $nom,
			":prenom" 	=> $prenom
		]);
		$db = null;

		echo "customer added";
	}catch(PDOException $e){
		echo $e->getMessage();
	}
});

$app->put('/api/customer/update/{id}', function(Request $req,Response $rep){

	$id = $req->getAttribute('id');
	$nom 	= $req->getParam('nom');
	$prenom = $req->getParam('prenom');
	$pass 	= $req->getParam('pass');
	$mail 	= $req->getParam('mail');

	$sql 	= "UPDATE clients SET 
						mail 	= :mail,
						pass 	= :pass,
						nom 	= :nom,
						prenom 	= :prenom
				WHERE id = $id
	";

	try {
		$db = new db;
		$db = $db->connect();
		$query = $db->prepare($sql);
		$query->execute([
			":mail" 	=> $mail,
			":pass" 	=> $pass,
			":nom" 		=> $nom,
			":prenom" 	=> $prenom
		]);
		$db = null;

		echo "customer updated";
	}catch(PDOException $e){
		echo $e->getMessage();
	}
});
 
$app->delete('/api/customer/delete/{id}', function(Request $req,Response $rep){

	$id = $req->getAttribute('id');

	$sql = "DELETE FROM clients WHERE id = $id";

	try {
		$db = new db;
		$db = $db->connect();
		$query = $db->prepare($sql);
		$query->execute();
		$db = null;

		echo "customer deleted";
	}catch(PDOException $e){
		echo $e->getMessage();
	}
});
?>