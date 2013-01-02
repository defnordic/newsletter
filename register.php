<!--
Es fehlen noch
*Captcha - brauchen wir das?
*Bestätigungsemail

newsletter=> \d content
                                             Table "public.content"
          Column           |            Type             |                      Modifiers                       
---------------------------+-----------------------------+------------------------------------------------------
 id                        | integer                     | not null default nextval('content_id_seq'::regclass)
 pref_id                   | integer                     | 
 content                   | text                        | 
 earliest_date_for_sending | timestamp without time zone | 
 latest_date_for_sending   | timestamp without time zone | 
 sent                      | boolean                     | 
 first_eyes_usr_id         | integer                     | 
 second_eyes_usr_id        | integer                     | 
 third_eyes_usr_id         | integer                     | 

newsletter=> \d users
                         Table "public.users"
 Column |  Type   |                     Modifiers                      
--------+---------+----------------------------------------------------
 id     | integer | not null default nextval('users_id_seq'::regclass)
 email  | text    | 
 prefs  | integer |
 confirmed | boolean | default false	 
 confirm_int | integer | default 0

newsletter=> \d admins
    Table "public.admins"
 Column |  Type   | Modifiers 
--------+---------+------------
 usr_id | integer | 
 rights | integer | 
-->

<?
require("db.php");
require("mail.php");
require("config.php");

$display = "#welcome_view {display:none;}";

$email = isset($_POST['email']) ? $_POST['email'] : '';
$bund = isset($_POST['bund']) ? $_POST['bund'] : '';
$bgld = isset($_POST['bgld']) ? $_POST['bgld'] : '';
$ktn = isset($_POST['ktn']) ? $_POST['ktn'] : '';
$noe = isset($_POST['noe']) ? $_POST['noe'] : '';
$ooe = isset($_POST['ooe']) ? $_POST['ooe'] : '';
$sbg = isset($_POST['sbg']) ? $_POST['sbg'] : '';
$stmk = isset($_POST['stmk']) ? $_POST['stmk'] : '';
$vlbg = isset($_POST['vlbg']) ? $_POST['vlbg'] : '';
$w = isset($_POST['w']) ? $_POST['w'] : '';
$submit = isset($_POST['submit']) ? $_POST['submit'] : '';

if($submit != "true"){
  goto end;
}

if($email == "") {
  $error = "Keine E-Mail-Adresse angegeben!";
  goto end;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  $error = "Diese E-Mail-Adresse ist ungültig!";
  goto end;
}

$db = new db($dbLang, $dbName);

$prefs = 1;
//if($bund == "bund") {$prefs += 1;}
if($bgld == "bgld") {$prefs += 2;}
if($ktn == "ktn") {$prefs += 4;}
if($noe == "noe") {$prefs += 8;}
if($ooe == "ooe") {$prefs += 16;}
if($sbg == "sbg") {$prefs += 32;}
if($stmk == "stmk") {$prefs += 64;}
if($vlbg == "vlbg") {$prefs += 128;}
if($w == "w") {$prefs += 512;}

$id = $db->query("SELECT id FROM users WHERE email = '$email' LIMIT 1");
if (count($id) > 0)
{
  $error = "Diese E-Mail-Adresse ist bereits für den Newsletter-Empfang eingetragen!";
  goto end;
}

$db->query("INSERT INTO users (email, prefs) VALUES ('$email', $prefs);");

$sid = $db->query("SELECT sid FROM users WHERE email = '$email'");
$sid = $sid[0]['sid'];

$checkmail_text = "Bitte bestätige deine E-Mail-Adresse mit einem Klick auf den folgenden Link:\n".change_link($sid,"confirm"). "\n";
mail_utf8($email, "[Piraten-Newsletter] Bestätigung deiner E-Mail-Adresse", $checkmail_text);

$db->close();

$display = "#form_view {display:none;}";

end:
?>

<!DOCTYPE html>
<html lang="de">
  <head>
    <meta charset="utf-8">
    <title>Piraten-Newsletter</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Hier können sich Interessenten und Mitglieder für den Newsletter der Piratenpartei Österreichs anmelden.">
    <meta name="author" content="Piratenpartei Österreichs">

    <!-- Le styles -->
    <link href="css/bootstrap.css" rel="stylesheet">
    <style type="text/css">
	body {
	background-color: #4c2582;
        padding-top: 60px;
        padding-bottom: 40px;
        }
	footer {
	color: white;
	}
<?echo $display;?>
    </style>

    <link href="css/bootstrap-responsive.css" rel="stylesheet">

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <!-- Fav and touch icons
    <link rel="shortcut icon" href="ico/favicon.ico">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="ico/apple-touch-icon-114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="ico/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="ico/apple-touch-icon-57-precomposed.png">-->
  </head>

  <body>

    <div class="container">
      <div class="row">
        <div class="span8">
	  <div id="welcome_view" class="well">
	    <h1>Danke für deine Anmeldung!</h1>
	    <p>An die von dir eingebene E-Mail-Adresse wird in Kürze eine Bestätigungsmail versendet.</p>
	  </div>
	  <div id="form_view" class="well">
	    <h1>Piraten-Newsletter</h1>
<?
if($error != "") {
  echo "<div class='alert alert-error'>".$error."</div>";
}
?>
	    <p>Hier kannst du dich zum Newsletter der Piratenpartei Österreichs schnell und einfach anmelden.<br>
	    Unsere aktuellen Datenschutzrichtlinien findest du hier: <a href="#">toter Link</a></p>
	    <form action="register.php" method="post">
		<h4>Bitte trage hier deine E-Mail-Adresse ein:<?echo $validemail;?></h4>
		<div class="input-prepend">
		  <span class="add-on">@</span>
		  <input id="inputEmail" type="text" name="email" placeholder="E-Mail-Adresse" value="<? echo $email; ?>">
		</div>
		<div>
		  <h4>Für welche Teile des Newsletters willst du dich registieren?</h4>
		  <input type="hidden" name="bund" value="bund" />
		  <label class="checkbox"><input type="checkbox" name="" value="" checked="checked" disabled>Bundesweite Informationen</label>
		  <label class="checkbox"><input type="checkbox" name="bgld" value="bgld">Burgenland</label>
		  <label class="checkbox"><input type="checkbox" name="ktn" value="ktn">Kärnten</label>
		  <label class="checkbox"><input type="checkbox" name="noe" value="noe">Niederösterreich</label>
		  <label class="checkbox"><input type="checkbox" name="ooe" value="ooe">Oberösterreich</label>
		  <label class="checkbox"><input type="checkbox" name="sbg" value="sbg">Salzburg</label>
		  <label class="checkbox"><input type="checkbox" name="stmk" value="stmk">Steiermark</label>
		  <label class="checkbox"><input type="checkbox" name="vlbg" value="vlbg">Vorarlberg</label>
		  <label class="checkbox"><input type="checkbox" name="w" value="w">Wien</label>
		</div>
              <input type="hidden" name="submit" value="true" />
	      <button type="submit" class="btn">Absenden</button>
	    </form>
	  </div>
        </div><!--/span-->
      </div><!--/row-->

      <footer>
        <p>Piratenpartei Österreichs, Lange Gasse 1/4, 1080 Wien</p>
      </footer>

    </div><!--/.fluid-container-->

    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="js/jquery.js"></script>
    <script src="js/bootstrap-transition.js"></script>
    <script src="js/bootstrap-alert.js"></script>
    <script src="js/bootstrap-modal.js"></script>
    <script src="js/bootstrap-dropdown.js"></script>
    <script src="js/bootstrap-scrollspy.js"></script>
    <script src="js/bootstrap-tab.js"></script>
    <script src="js/bootstrap-tooltip.js"></script>
    <script src="js/bootstrap-popover.js"></script>
    <script src="js/bootstrap-button.js"></script>
    <script src="js/bootstrap-collapse.js"></script>
    <script src="js/bootstrap-carousel.js"></script>
    <script src="js/bootstrap-typeahead.js"></script>
  </body>
</html>
