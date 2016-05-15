<?php
/**
* @author: Jimmy Latour jimmy.eoxia@gmail.com
* Vérifie si tous les $_POST sont sécurisés.
* S'il n'est pas sécurisé il sera affiché
* Et une erreur sera exécuter sur travis-ci
*/

define('END_TEST', "/^.*\.php$/");

echo "[+] Starting Ajax Tests" . PHP_EOL . PHP_EOL;

// Search for test files
$unitList = searchFiles('../', END_TEST);
$string_post_unsecured = array();
$total_unsecured_line = 0;
$pattern = '#\$_POST|\$_GET|\$_REQUEST#';

// Loop on unitList
foreach($unitList as $test)
{
	// echo "[+] Testing -> " . $test . PHP_EOL;
  if ( $test != '../script/request.test.php' ) {
    $file = file_get_contents( $test );
    $string_post_unsecured[$test] = array();
    $lines = explode( PHP_EOL, $file );

    if ( !empty( $lines ) ) {
      foreach ( $lines as $key => $line ) {
        if ( preg_match( $pattern, $line ) ) {
          $lines[$key] = preg_replace( '#!empty\(.+?\$_POST|\$_GET|$_REQUEST\[\'.+\'\].+?\) \?#isU', '', $lines[$key] );
          if ( !preg_match( '#sanitize_.+#', $lines[$key] ) &&
            !preg_match( '#\*#', $lines[$key] ) &&
            !preg_match( '#\\/\/#', $lines[$key] ) ) {
              $string_post_unsecured[$test][$key + 1] = htmlentities($lines[$key]);
              $total_unsecured_line++;
          }
        };
      }
    }
  }
}

/* Recursively search files
	folder = string => where to search
	patter = string => regexp for what to search
*/
function searchFiles($folder, $pattern)
{
	$dir = new RecursiveDirectoryIterator($folder);
	$ite = new RecursiveIteratorIterator($dir);
	$files = new RegexIterator($ite, $pattern, RegexIterator::GET_MATCH);
	$fileList = array();
	foreach($files as $file)
	{
		$fileList[] = $file[0];
	}
	return $fileList;
}

?>
<?php echo "[+] Total unsecured line : " . $total_unsecured_line . PHP_EOL; ?>

<?php
if ( !empty( $string_post_unsecured ) ) {
  foreach( $string_post_unsecured as $name_file => $file ) {
    if ( !empty( $file ) ) {
      echo "[+] File : " . $name_file . ' => Unsecured $_POST|$_GET|$_REQUEST ' . count( $file ) . PHP_EOL;
      foreach ( $file as $line => $content ) {
        echo "[+] Line : " . $line . " => " . $content . PHP_EOL;
        trigger_error( "[+] Line : " . $line . " => " . $content );
      }
    }
  }
}

echo "[+] Ajax Tests Finished" . PHP_EOL; ?>
