<?php

set_time_limit(0);
ini_set("memory_limit", "2048M");

error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', 1);

session_start();

// * * * FIX * * *
// * * * Warning: strtotime(), date(), etc...: It is not safe to rely on the system's timezone settings.  * * *
// * * * You are *required* to use the date.timezone setting or the date_default_timezone_set() function. * * *
$timezone_identifier = "America/Argentina/Buenos_Aires";
date_default_timezone_set($timezone_identifier);


define("DIRTOARRAY_SORT_BY_NAME", 1);
define("DIRTOARRAY_SORT_BY_SIZE", 2);
define("DIRTOARRAY_SORT_BY_TIME", 3);

define("SHOW_MOVIES_FROM_TXT_FILE", 1);
define("SHOW_MOVIES_FROM_PATH_DIR", 2);


  if ( ! function_exists('show_between_pre_tag')) {
  /**
  * Show between PRE tags
  * Show an array between HTML PRE tags with title if this exist
  *
  * @access public
  * @param $value array
  * @param $title string
  * @param $color string
  * @return void
  */
    function show_between_pre_tag($value=array(), $title="", $color="#000000"){
      if($title != "")
        echo("<br>".$title."<br>");
      echo('<pre style="color: '.$color.'; text-align: left; font-size: 12px;">'); print_r($value); echo('</pre>');
    }
  }

  /**
   * return date in d-m-y format in MySql date format
   *
   * @access public
   * @param string $fecha
   * @return string in MySql date format yyyy-mm-dd
   */
  function _get_mysql_date_format ($fecha) {
    $return_value=null;

    $_fecha_aux = explode("-", $fecha);
    //show_between_pre_tag($_fecha_hora_aux, "\$_fecha_hora_aux", "#ffffff");
    $_fecha = array();
    if(is_array($_fecha_aux)) {
      foreach ($_fecha_aux as $idx_arr => $val_fecha) {
        if( ( ! empty($val_fecha)) && ($val_fecha != " ") ) {
          $_fecha[] = $val_fecha;
        }
      }
      //show_between_pre_tag($_fecha, "\$_fecha", "#ffffff");

      if(count($_fecha)==3) {
        $dd = $_fecha[0]; $month = $_fecha[1]; $yyyy = $_fecha[2];

        $utime = mktime(0, 1, 1, $month, $dd, $yyyy);
        if ( ($utime !== false) && ($utime !== -1) ) {
          $final_date = date("Y-m-d", $utime);
          //echo("<span style=\" color: white; \">\$final_date: ".$final_date."</span><br/>");
          $return_value = $final_date;
        }
      }
    }

    return $return_value;
  }


  function _get_html_table_from_array ($in_array=array(), $title="") {
    //show_between_pre_tag($in_array);
    //var_dump($in_array);
    $first_row_showed=false; // border="1px"
    $count_fields = 1;
    $count_rows = 0;
    //border: 1px solid silver;
    $out='<table cellpadding = "2" cellspacing="1" border="1" style="font-size: 10px; font-family: Verdana; width: auto;">'."\n";
    foreach ($in_array as $idx_array => $val_array) {
      //show_between_pre_tag($val_array);
      if( ! $first_row_showed) {
        $_fieldNames = array_keys($val_array);
        $count_fields = count($_fieldNames);
        $first_row_showed = true;
        $out.='<tr><td colspan="'.($count_fields+1).'" style="text-align: left; font-weight: bold;">'.$title.' - '.$count_fields.' fields'.'</td></tr>';
        $out.="<tr>";
        $out.='<td style="text-align: center; font-weight: bold;" class="abm_reg12">idx_arr</td>';
        //border: 1px solid silver;
        foreach ($_fieldNames as $idx_field => $val_fieldName) {
         $out.='<td style="text-align: center; font-weight: bold; white-space: nowrap; " class="abm_reg12">'.$val_fieldName."</td>";
        }
        $out.="</tr>\n";
      }

      $out.="<tr class=\"abm_reg1\">";
      $out.='<td align="right" class="txt_cont_normal">'.$idx_array."</td>";
      foreach ($val_array as $idx_field => $val_fieldValue) {
        // style="border: 1px solid silver; "
        $out.='<td align="right" class="txt_cont_normal" style="white-space: nowrap; ">';
        if ($val_fieldValue === null) {
          $out.="<span style=\"font-style: italic;\">NULL</span>";
        }
        else {
          if( (($val_fieldValue === "") || ($val_fieldValue === " ") || ($val_fieldValue == "\n") || ($val_fieldValue == "\r\n"))
            AND (is_string($val_fieldValue)) ) {
            $out.="<span style=\"font-style: italic;\">Empty String</span>";
          }
          else {
            if (is_array($val_fieldValue)) {
              //$out.=_get_html_table_from_array(array($idx_field => $val_fieldValue));
              $out.=$val_fieldValue;
            }
            else {
              $out.=$val_fieldValue;
              //$out.=var_dump($val_fieldValue);
            }
          }
        }
        $out.="</td>";
      }
      $out.="</tr>\n";

      $count_rows++;
    }
    $out.="<tr><td colspan=\"".($count_fields+1)."\" style=\"text-align: left; color: black;\">Total: <b>$count_rows</b></td></tr>\n";
    $out.="</table>\n";

    return $out;
  }

  function _show_html_table_from_array ($in_array=array(), $title="") { echo(_get_html_table_from_array($in_array, $title)); }


  function dirToArray($dir, $sort_criteria=DIRTOARRAY_SORT_BY_NAME, $sort_order=SORT_ASC) {

    $_result = array();

    $cdir = scandir($dir);
    foreach ($cdir as $key => $value) {
      if (!in_array($value,array(".",".."))) {
        if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) {
          $_result[$value] = dirToArray($dir . DIRECTORY_SEPARATOR . $value);
        }
        else {
          $_file_stat = stat($dir . DIRECTORY_SEPARATOR . $value);
          // show_between_pre_tag($_file_stat, "\$_file_stat", "silver");
          $_result[] = ["name" => $value, "size" => $_file_stat["size"], "mtime" => $_file_stat["mtime"]];
        }
      }
    }

    // * * * Ordenar lista de Archivos * * *
    // * * * Ordenar Lista * * * // Obtain a list of columns
    $_order_info = array();
    foreach ($_result as $key => $_row) {
      switch ($sort_criteria) {
        case DIRTOARRAY_SORT_BY_SIZE: $_order_info[$key]  = $_row['size']; break;
        case DIRTOARRAY_SORT_BY_TIME: $_order_info[$key]  = $_row['mtime']; break;

        default: $_order_info[$key]  = $_row['name']; break;
      }

    }
    // Sort the data with $_order_info in $sort_order mode // Add $_result as the last parameter, to sort by the common key
    array_multisort($_order_info, $sort_order, $_result);

    return $_result;
  }

  /**
   * Show VAR content between HTML SPAN tags with specific color. "#rrggbb"
   *
   * @access public
   * @param $var_name string
   * @param $var_value mixed
   * @param $color string
   * @return void
   */
  function show_var_content($var_name, $var_value, $color="lightgray") {
    echo("<span style=\"color: ".$color.";\">".$var_name." = ".$var_value."</span><br/>\n");
  }


  /**
   * @param $user_agent null
   * @return string
   */
  function getOS($user_agent = null)
  {
    if(!isset($user_agent) && isset($_SERVER['HTTP_USER_AGENT'])) {
      $user_agent = $_SERVER['HTTP_USER_AGENT'];
    }

    // https://stackoverflow.com/questions/18070154/get-operating-system-info-with-php
    $os_array = [
      'windows nt 10'                              =>  'Windows 10',
      'windows nt 6.3'                             =>  'Windows 8.1',
      'windows nt 6.2'                             =>  'Windows 8',
      'windows nt 6.1|windows nt 7.0'              =>  'Windows 7',
      'windows nt 6.0'                             =>  'Windows Vista',
      'windows nt 5.2'                             =>  'Windows Server 2003/XP x64',
      'windows nt 5.1'                             =>  'Windows XP',
      'windows xp'                                 =>  'Windows XP',
      'windows nt 5.0|windows nt5.1|windows 2000'  =>  'Windows 2000',
      'windows me'                                 =>  'Windows ME',
      'windows nt 4.0|winnt4.0'                    =>  'Windows NT',
      'windows ce'                                 =>  'Windows CE',
      'windows 98|win98'                           =>  'Windows 98',
      'windows 95|win95'                           =>  'Windows 95',
      'win16'                                      =>  'Windows 3.11',
      'mac os x 10.1[^0-9]'                        =>  'Mac OS X Puma',
      'macintosh|mac os x'                         =>  'Mac OS X',
      'mac_powerpc'                                =>  'Mac OS 9',
      'linux'                                      =>  'Linux',
      'ubuntu'                                     =>  'Linux - Ubuntu',
      'iphone'                                     =>  'iPhone',
      'ipod'                                       =>  'iPod',
      'ipad'                                       =>  'iPad',
      'android'                                    =>  'Android',
      'blackberry'                                 =>  'BlackBerry',
      'webos'                                      =>  'Mobile',

      '(media center pc).([0-9]{1,2}\.[0-9]{1,2})'=>'Windows Media Center',
      '(win)([0-9]{1,2}\.[0-9x]{1,2})'=>'Windows',
      '(win)([0-9]{2})'=>'Windows',
      '(windows)([0-9x]{2})'=>'Windows',

      // Doesn't seem like these are necessary...not totally sure though..
      //'(winnt)([0-9]{1,2}\.[0-9]{1,2}){0,1}'=>'Windows NT',
      //'(windows nt)(([0-9]{1,2}\.[0-9]{1,2}){0,1})'=>'Windows NT', // fix by bg

      'Win 9x 4.90'=>'Windows ME',
      '(windows)([0-9]{1,2}\.[0-9]{1,2})'=>'Windows',
      'win32'=>'Windows',
      '(java)([0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,2})'=>'Java',
      '(Solaris)([0-9]{1,2}\.[0-9x]{1,2}){0,1}'=>'Solaris',
      'dos x86'=>'DOS',
      'Mac OS X'=>'Mac OS X',
      'Mac_PowerPC'=>'Macintosh PowerPC',
      '(mac|Macintosh)'=>'Mac OS',
      '(sunos)([0-9]{1,2}\.[0-9]{1,2}){0,1}'=>'SunOS',
      '(beos)([0-9]{1,2}\.[0-9]{1,2}){0,1}'=>'BeOS',
      '(risc os)([0-9]{1,2}\.[0-9]{1,2})'=>'RISC OS',
      'unix'=>'Unix',
      'os/2'=>'OS/2',
      'freebsd'=>'FreeBSD',
      'openbsd'=>'OpenBSD',
      'netbsd'=>'NetBSD',
      'irix'=>'IRIX',
      'plan9'=>'Plan9',
      'osf'=>'OSF',
      'aix'=>'AIX',
      'GNU Hurd'=>'GNU Hurd',
      '(fedora)'=>'Linux - Fedora',
      '(kubuntu)'=>'Linux - Kubuntu',
      '(ubuntu)'=>'Linux - Ubuntu',
      '(debian)'=>'Linux - Debian',
      '(CentOS)'=>'Linux - CentOS',
      '(Mandriva).([0-9]{1,3}(\.[0-9]{1,3})?(\.[0-9]{1,3})?)'=>'Linux - Mandriva',
      '(SUSE).([0-9]{1,3}(\.[0-9]{1,3})?(\.[0-9]{1,3})?)'=>'Linux - SUSE',
      '(Dropline)'=>'Linux - Slackware (Dropline GNOME)',
      '(ASPLinux)'=>'Linux - ASPLinux',
      '(Red Hat)'=>'Linux - Red Hat',
      // Loads of Linux machines will be detected as unix.
      // Actually, all of the linux machines I've checked have the 'X11' in the User Agent.
      //'X11'=>'Unix',
      '(linux)'=>'Linux',
      '(amigaos)([0-9]{1,2}\.[0-9]{1,2})'=>'AmigaOS',
      'amiga-aweb'=>'AmigaOS',
      'amiga'=>'Amiga',
      'AvantGo'=>'PalmOS',
      //'(Linux)([0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,3}(rel\.[0-9]{1,2}){0,1}-([0-9]{1,2}) i([0-9]{1})86){1}'=>'Linux',
      //'(Linux)([0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,3}(rel\.[0-9]{1,2}){0,1} i([0-9]{1}86)){1}'=>'Linux',
      //'(Linux)([0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,3}(rel\.[0-9]{1,2}){0,1})'=>'Linux',
      '[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,3})'=>'Linux',
      '(webtv)/([0-9]{1,2}\.[0-9]{1,2})'=>'WebTV',
      'Dreamcast'=>'Dreamcast OS',
      'GetRight'=>'Windows',
      'go!zilla'=>'Windows',
      'gozilla'=>'Windows',
      'gulliver'=>'Windows',
      'ia archiver'=>'Windows',
      'NetPositive'=>'Windows',
      'mass downloader'=>'Windows',
      'microsoft'=>'Windows',
      'offline explorer'=>'Windows',
      'teleport'=>'Windows',
      'web downloader'=>'Windows',
      'webcapture'=>'Windows',
      'webcollage'=>'Windows',
      'webcopier'=>'Windows',
      'webstripper'=>'Windows',
      'webzip'=>'Windows',
      'wget'=>'Windows',
      'Java'=>'Unknown',
      'flashget'=>'Windows',

      // delete next line if the script show not the right OS
      //'(PHP)/([0-9]{1,2}.[0-9]{1,2})'=>'PHP',
      'MS FrontPage'=>'Windows',
      '(msproxy)/([0-9]{1,2}.[0-9]{1,2})'=>'Windows',
      '(msie)([0-9]{1,2}.[0-9]{1,2})'=>'Windows',
      'libwww-perl'=>'Unix',
      'UP.Browser'=>'Windows CE',
      'NetAnts'=>'Windows',
    ];

    // https://github.com/ahmad-sa3d/php-useragent/blob/master/core/user_agent.php
    $arch_regex = '/\b(x86_64|x86-64|Win64|WOW64|x64|ia64|amd64|ppc64|sparc64|IRIX64)\b/ix';
    $arch = preg_match($arch_regex, $user_agent) ? '64' : '32';

    foreach ($os_array as $regex => $value) {
      if (preg_match('{\b('.$regex.')\b}i', $user_agent)) {
        return $value.' x'.$arch;
      }
    }

    return 'Unknown';
  }



  function getBrowser() {

    $user_agent = $_SERVER['HTTP_USER_AGENT'];

    $browser        = "Unknown Browser";

    $browser_array = array(
      '/msie/i'      => 'Internet Explorer',
      '/firefox/i'   => 'Firefox',
      '/safari/i'    => 'Safari',
      '/chrome/i'    => 'Chrome',
      '/edge/i'      => 'Edge',
      '/opera/i'     => 'Opera',
      '/netscape/i'  => 'Netscape',
      '/maxthon/i'   => 'Maxthon',
      '/konqueror/i' => 'Konqueror',
      '/mobile/i'    => 'Handheld Browser'
    );

    foreach ($browser_array as $regex => $value)
      if (preg_match($regex, $user_agent))
        $browser = $value;

        return $browser;
  }

  function _show_formatted_file_size($file_size) {
    if($file_size <= 0) return (int)0;

    $_units = ["Gb", "Mb", "Kb"]; // , "By"];
    $_sizes = [1024**3, 1024**2, 1024]; // , 0];

    foreach ($_sizes as $id_arr => $size_value) {
      if ($file_size >= $size_value) {
        return number_format($file_size / $size_value, 2, ",", ".")." ".$_units[$id_arr];
        // return "(".$file_size.")"."[".$size_value."]".number_format($file_size / $size_value, 3, ",", ".")." ".$_units[$id_arr]."[".$id_arr."]";
      };
    }

    return number_format($file_size, 0, ",", ".")." By";
  }


  // From: https://www.php.net/manual/en/function.filesize.php#121406
  /**
   * Return file size (even for file > 2 Gb)
   * For file size over PHP_INT_MAX (2 147 483 647), PHP filesize function loops from -PHP_INT_MAX to PHP_INT_MAX.
   *
   * @param string $path Path of the file
   * @return mixed File size or false if error
   */
  function realFileSize($path, $actual_size) {
    if (!file_exists($path))
      return false;

    $size = filesize($path);
    if ($size == $actual_size)
      return $size;

    if (!($file = fopen($path, 'rb')))
      return false;

    if ($size >= 0) {//Check if it really is a small file (< 2 GB)
      if (fseek($file, 0, SEEK_END) === 0) {//It really is a small file
        fclose($file);
        return $size;
      }
    }

    //Quickly jump the first 2 GB with fseek. After that fseek is not working on 32 bit php (it uses int internally)
    $size = PHP_INT_MAX - 1;
    if (fseek($file, PHP_INT_MAX - 1) !== 0) {
      fclose($file);
      return false;
    }

    $length = 1024 * 1024;
    while (!feof($file)) {//Read the file until end
      $read = fread($file, $length);
      $size = bcadd($size, $length);
    }
    $size = bcsub($size, $length);
    $size = bcadd($size, strlen($read));

    fclose($file);
    return $size;
  }


  // best converting the negative number with File Size .
  // does not work with files greater than 4GB
  //
  // specifically for 32 bit systems. limit conversions filsize is 4GB or
  // 4294967296. why we get negative numbers? by what the file
  // pointer of the meter must work with the PHP MAX value is 2147483647.
  // Offset file : 0 , 1 , 2 , 3 , ... 2147483647 = 2GB
  // to go higher up the 4GB negative numbers are used
  // and therefore after 2147483647, we will -2147483647
  // -2147483647,  -2147483646, -2147483645, -2147483644 ... 0 = 4GB
  // therefore 0, 2147483647 and -2147483647 to 0. all done 4GB = 4294967296
  // the first offset to 0 and the last offset to 0 of 4GB should be added in
  // your compute, so "+ 2" for the number of bytes exate .

  function file_size_32b($file) {
    $filez = filesize($file);
    if($filez < 0) {
      return (($filez + PHP_INT_MAX) + PHP_INT_MAX + 2);
    }
    else {
      return $filez;
    }
  }


  /*
   * from: https://www.php.net/manual/en/function.shell-exec.php#88052
   */
  function file_size_shell($file_name) {
    // echo($file_name."<br/>"); // exit();

    $file_size = 0;
    $shell_command = "dir \"".$file_name."\""; // echo(get_current_user()." : ".$shell_command."<br/>");

    ob_start();
    $dir = shell_exec($shell_command);
    if (is_null($dir)) {   // B: does not exist
      // do whatever you want with the stderr output here
      echo("dir does not exist.<br/>");
    }
    else {  // B: exists and $dir holds the directory listing
      // do whatever you want with it here

      $_dir = explode("\n", $dir);
      // show_between_pre_tag($_dir, "\$_dir", "silver");
      $file_size_dir_str = $_dir[6];
      // show_between_pre_tag($file_size_dir_str, "\$file_size_dir_str", "silver");
      if( (isset($_dir[6])) && (preg_match("/^.*1 File\(s\)(.*) bytes.*$/", $file_size_dir_str, $_matches)) ) {
        // show_between_pre_tag($_matches, "\$_matches", "silver");
        if (isset($_matches[1])) {
          $file_size_str = $_matches[1];
          $file_size = (float)str_replace(".", "", $file_size_str);
          // var_dump($file_size); echo("<br/>");
          /*
          echo("<span color=\"silver\">");
          for ($i = 0; $i < strlen($file_size_str); $i++) {
            echo(ord($file_size_str[$i])."-");
          }
          echo("</span>");
          */
        }
      }
    }
    ob_flush();
    ob_end_clean();   // get rid of the evidence :-)
    // echo(_show_formatted_file_size($file_size)."<br/>");
    return $file_size;
  }

?>