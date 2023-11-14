<?php

/**
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2017 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/
 
if (!defined('ACCESS')) {
    header('Location: /');
    exit;
}
require (ROOT.'etc/content.config.php');

function confirm($id, $secret_key, $success = false)
{
global $db, $config, $core, $url, $headTag, $content_conf, $lang;

		$query = $db->query("SELECT * FROM JMY_doc WHERE id = '".$db->safesql($id)."' AND responsible_persons LIKE '%\"secret_key\":\"" . $db->safesql($secret_key) . "\"%' LIMIT 1");
		$r = $db->getRow($query);
		if($r)
		{	
			$json = json_decode($r["responsible_persons"], true);
			if($success){
				foreach($json as $key => $val){
					if($val["secret_key"] == $secret_key){
						$json[$key]["date"] = date("Y-m-d H:i:s");
						$json[$key]["status"] = "2";
					}
				}
				$res = json_encode($json, JSON_UNESCAPED_UNICODE);
				$db->query("UPDATE JMY_doc SET responsible_persons = '".$res."' WHERE id = '".$id."' LIMIT 1");
					$qq = $db->query("SELECT responsible_persons FROM JMY_doc WHERE id = '".$id."' LIMIT 1");
					$q = $db->getRow($qq);
					$jj = json_decode($r["responsible_persons"], true);
					$count = count($jj)-1;
					foreach($jj as $key => $val){
						if($val["status"] == "2"){
							$count--;
						}
					}
					if($count <= 0){
						$db->query("UPDATE JMY_doc SET status = '2' WHERE id = '".$id."' LIMIT 1");
					}
			}
			?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Подтверждение документа</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
				<link rel="shortcut icon" href="/files/doc/favicon.ico" type="image/x-icon">
		<style>
		/*
 * Globals
 */

/* Links */
a,
a:focus,
a:hover {
  color: #fff;
}

/* Custom default button */
.btn-secondary,
.btn-secondary:hover,
.btn-secondary:focus {
  color: #333;
  text-shadow: none; /* Prevent inheritance from `body` */
  background-color: #fff;
  border: .05rem solid #fff;
}


/*
 * Base structure
 */

html,
body {
  height: 100%;
  background-color: #333;
}

body {
  display: -ms-flexbox;
  display: -webkit-box;
  display: flex;
  -ms-flex-pack: center;
  -webkit-box-pack: center;
  justify-content: center;
  color: #fff;
  text-shadow: 0 .05rem .1rem rgba(0, 0, 0, .5);
  box-shadow: inset 0 0 5rem rgba(0, 0, 0, .5);
}

.cover-container {
  max-width: 42em;
}


/*
 * Header
 */
.masthead {
  margin-bottom: 2rem;
}

.masthead-brand {
  margin-bottom: 0;
}

.nav-masthead .nav-link {
  padding: .25rem 0;
  font-weight: 700;
  color: rgba(255, 255, 255, .5);
  background-color: transparent;
  border-bottom: .25rem solid transparent;
}

.nav-masthead .nav-link:hover,
.nav-masthead .nav-link:focus {
  border-bottom-color: rgba(255, 255, 255, .25);
}

.nav-masthead .nav-link + .nav-link {
  margin-left: 1rem;
}

.nav-masthead .active {
  color: #fff;
  border-bottom-color: #fff;
}

@media (min-width: 48em) {
  .masthead-brand {
    float: left;
  }
  .nav-masthead {
    float: right;
  }
}


/*
 * Cover
 */
.cover {
  padding: 0 1.5rem;
}
.cover .btn-lg {
  padding: .75rem 1.25rem;
  font-weight: 700;
}


/*
 * Footer
 */
.mastfoot {
  color: rgba(255, 255, 255, .5);
}



.flex-column{
	justify-content: center;
}
.lead{
	display: flex;
	justify-content: center;
}
		</style>
</head>

<body>
<?php
			$flag = true;
				foreach($json as $key => $val){
					if($val["secret_key"] == $secret_key){
						if($val["status"] == 2){
							$flag = false;
						}
					}
				}
			?>
    <div class="cover-container d-flex h-100 p-3 mx-auto flex-column">
        <main role="main" class="inner cover">
            <h1 class="cover-heading"><?php echo $flag ? "Подтвердить документ" : "Документ подтвержден!"; ?></h1>
            <p class="lead"></p>
            <p class="lead">
			<?php
			if($flag){
				echo '<a href="/doc/success/'.$id."/".$secret_key."/".'" class="btn btn-lg btn-secondary">Подтвердить документ</a>';
			}
			?>
            </p>
        </main>
    </div>


    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous">
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous">
    </script>
</body>

</html>

<?php	
		}
		else
		{
			location('/');
		}
	
	exit();
}
 
global $lang;
switch(isset($url[1]) ? $url[1] : null) 
{
	default:
		
	break;
	case "confirm":
		if(isset($url[2], $url[3])) 
		{
			$id = $url[2];
			$secret_key = $url[3];
			confirm($id, $secret_key);
		}
		else
		{
			location('/');
		}
	break;
	case "last":
		$query = $db->query("SELECT * FROM JMY_doc ORDER BY id DESC LIMIT 1");
		$r = $db->getRow($query);
		
		location(config('app.url').'/administration/module/doc/print/doc/'.$r['id']);
		exit;
	break;	
	case "success":
	if(isset($url[2], $url[3])) 
	{
		$id = $url[2];
		$secret_key = $url[3];
		$success = true;
		confirm($id, $secret_key, $success);
	}
	else
	{
		location('/');
	}
break;

}