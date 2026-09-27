<?php
/** Adminer - Compact database management
* @link https://www.adminer.org/
* @author Jakub Vrana, https://www.vrana.cz/
* @copyright 2007 Jakub Vrana
* @license https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
* @license https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2 (one or other)
* @version 6.1.1
*/namespace
Adminer;if(isset($_GET["status"]))$_GET["variables"]=$_GET["status"];if(isset($_GET["import"]))$_GET["sql"]=$_GET["import"];const
VERSION="6.1.1";error_reporting(24575);set_error_handler(function($Xc,$Zc){return!!preg_match('~^Undefined (array key|offset|index)~',$Zc);},E_WARNING|E_NOTICE);$zd=!preg_match('~^(unsafe_raw)?$~',ini_get("filter.default"));if($zd||ini_get("filter.default_flags")){foreach(array('_GET','_POST','_COOKIE','_SERVER')as$X){$Yk=filter_input_array(constant("INPUT$X"),FILTER_UNSAFE_RAW);if($Yk)$$X=$Yk;}}$_COOKIE=array_filter($_COOKIE,'is_scalar');if(function_exists("mb_internal_encoding"))mb_internal_encoding("8bit");function
connection($f=null){return($f?:Db::$instance);}function
adminer(){return
Adminer::$instance;}function
driver(){return
Driver::$instance;}function
connect(){$Nb=adminer()->credentials();$K=Driver::connect($Nb[0],$Nb[1],$Nb[2]);return(is_object($K)?$K:null);}function
idf_unescape($t){if(!preg_match('~^[`\'"[]~',$t))return$t;$sf=substr($t,-1);return
str_replace($sf.$sf,$sf,substr($t,1,-1));}function
q($R){return
connection()->quote($R);}function
idx($_a,$x,$i=null){return($_a&&array_key_exists($x,$_a)?$_a[$x]:$i);}function
number($X){return
preg_replace('~[^0-9]+~','',$X);}function
int_type(){return'(tiny|small|medium|big)?int(eger|\d)?';}function
number_type(){return'(^('.int_type().'|decimal|numeric|number|real|(binary_|half_|scaled_)?float\d?|(binary_)?double( precision)?|(small)?money)$)';}function
text_type(){return'char|text'.(JUSH=="sql"?'|enum|set':'');}function
is_user_type($V){return
in_array($V,idx(driver()->structuredTypes(),lang(0),array()));}function
full_type_sql(array$k){$V=$k["type"];return(is_user_type($V)?idf_escape($V).substr($k["full_type"],strlen($V)):$k["full_type"]);}function
is_searchable(array$k,array$X){if(!isset($k["privileges"]["where"]))return
false;if(preg_match('~NULL$~',$X["op"]))return
true;$V=$k["type"];$Ui=$X["val"];$Ra='binary$|bytea|raw|image|bfile|^vector$'.(JUSH=="mssql"?'|^timestamp$':'|^bit').(JUSH=="oracle"?'|^blob|^long|rowid':'');if(preg_match("~$Ra~",$V))return
false;if(preg_match(number_type(),$V)){$D='-?\d+(\.\d+)?';return(bool)preg_match('~^'.$D.(preg_match('~IN$~',$X["op"])?"( *, *$D)*":'').'$~',$Ui);}if(preg_match('~^(small)?date|^timestamp~',$V))return(bool)preg_match('~^\d+-\d+-\d+~',$Ui);if(preg_match('~^time~',$V))return(bool)preg_match('~^\d+:\d+~',$Ui);if(preg_match('~^bool~',$V)||(JUSH=="mssql"&&$V=="bit"))return(bool)preg_match('~^(t|f|true|false|[01])$~i',$Ui);return
true;}function
remove_slashes(array$ul,$zd=false){$K=array();foreach($ul
as$x=>$X)$K[stripslashes($x)]=(is_array($X)?remove_slashes($X,$zd):($zd?$X:stripslashes($X)));return$K;}function
bracket_escape($t,$Ka=false){static$Ek=array(':'=>':1',']'=>':2','['=>':3','"'=>':4','='=>':5');return
strtr($t,($Ka?array_flip($Ek):$Ek));}function
url_escape($R){static$Ek=array();if(!$Ek){$Ek=array(' '=>'+');foreach(str_split("\"'<>#%&+=?".ini_get("arg_separator.input"))as$bb)$Ek[$bb]=sprintf('%%%02X',ord($bb));for($r=0;$r<256;$r++){if($r<32||$r>126)$Ek[chr($r)]=sprintf('%%%02X',$r);}}return
strtr((string)$R,$Ek);}function
min_version($xl,$Lf="",$f=null){$f=connection($f);$jj=$f->server_info;if($Lf&&preg_match('~([\d.]+)-MariaDB~',$jj,$B)){$jj=$B[1];$xl=$Lf;}return$xl&&version_compare($jj,$xl)>=0;}function
charset(Db$Ab){return(min_version("5.5.3",0,$Ab)?"utf8mb4":"utf8");}function
ini_set($dh,$Y){return(function_exists('ini_set')?\ini_set($dh,$Y):false);}function
ini_bool($Oe){$X=ini_get($Oe);return(preg_match('~^(on|true|yes)$~i',$X)||(int)$X);}function
ini_bytes($Oe){$X=ini_get($Oe);switch(strtolower(substr($X,-1))){case'g':$X=(int)$X*1024;case'm':$X=(int)$X*1024;case'k':$X=(int)$X*1024;}return$X;}function
max_input_vars($L,$rh){$Of=(int)ini_get("max_input_vars");return($Of?(int)floor(($Of-$rh)/$L):0);}function
max_input_vars_error(){$Oe="max_input_vars";return
lang(1,"<b>$Oe = ".ini_get($Oe)."</b>");}function
sid(){static$K;if($K===null)$K=(SID&&!($_COOKIE&&ini_bool("session.use_cookies")));return$K;}function
set_password($wl,$P,$ol,$G){$_SESSION["pwds"][$wl][$P][$ol]=($_COOKIE["adminer_key"]&&is_string($G)?array(encrypt_string($G,$_COOKIE["adminer_key"])):$G);}function
get_password(){$K=get_session("pwds");if(is_array($K))$K=($_COOKIE["adminer_key"]?decrypt_string($K[0],$_COOKIE["adminer_key"]):false);return$K;}function
get_val($I,$k=0,$_b=null){$_b=connection($_b);$J=$_b->query($I);if(!is_object($J))return
false;$L=$J->fetch_row();return($L?$L[$k]:false);}function
get_vals($I,$d=0){$K=array();$J=connection()->query($I);if(is_object($J)){while($L=$J->fetch_row())$K[]=$L[$d];}return$K;}function
get_key_vals($I,$f=null,$mj=true){$f=connection($f);$K=array();$J=$f->query($I);if(is_object($J)){while($L=$J->fetch_row()){if($mj)$K[$L[0]]=$L[1];else$K[]=$L[0];}}return$K;}function
get_rows($I,$f=null,$j="<p class='error'>"){$_b=connection($f);$K=array();$J=$_b->query($I);if(is_object($J)){while($L=$J->fetch_assoc())$K[]=$L;}elseif(!$J&&!$f&&$j&&(defined('Adminer\PAGE_HEADER')||$j=="-- "))echo$j.adminer()->error()."\n";return$K;}function
unique_array($L,array$v){foreach($v
as$u){if(preg_match("~^(PRIMARY|UNIQUE)$~",$u["type"])&&!$u["partial"]){$K=array();foreach($u["columns"]as$x){if(!isset($L[$x]))continue
2;$K[$x]=$L[$x];}return$K;}}}function
where_function($Rd,$d,array$k){if($Rd=="md5")return
driver()->md5($d,$k)?:$d;return(in_array($Rd,driver()->functions)||in_array($Rd,driver()->grouping)?apply_sql_function($Rd,$d):$d);}function
where(array$Z,array$l=array()){$K=array();foreach((array)$Z["where"]as$x=>$X){$x=bracket_escape($x,true);$d=idf_escape($x);$k=idx($l,$x,array());$td=$k["type"];$af=$k&&(is_blob($k)||preg_match('~binary~',$td));$K[]=$d.($af&&!is_utf8($X)?" = ".driver()->quoteBinary($X):(JUSH=="sql"&&$td=="json"?" = CAST(".q($X)." AS JSON)":(JUSH=="pgsql"&&preg_match('~^jsonb?$~',$k["full_type"])?"::jsonb = ".q($X)."::jsonb":(JUSH=="sql"&&is_numeric($X)&&preg_match('~\.~',$X)?" LIKE ".q($X):(JUSH=="mssql"&&strpos($td,"datetime")===false?" LIKE ".q(preg_replace('~[_%[]~','[\0]',$X)):" = ".unconvert_field($k,q($X)))))));if(JUSH=="sql"&&preg_match('~char|text~',$td)&&preg_match("~[^ -@]~",$X))$K[]="$d = ".q($X)." COLLATE ".charset(connection())."_bin";}foreach((array)$Z["null"]as$x)$K[]=idf_escape($x)." IS NULL";foreach((array)$Z["col"]as$r=>$c){$X=idx($Z["val"],$r);$K[]=where_function(idx($Z["fun"],$r),idf_escape($c),idx($l,$c,array())).($X!==null?" = ".q($X):" IS NULL");}return
implode(" AND ",$K);}function
where_columns(array$l){$K=array();foreach((array)$_GET["null"]as$x)$K[$x]=true;foreach(array_keys((array)$_GET["where"])as$x)$K[bracket_escape($x,true)]=true;foreach((array)$_GET["col"]as$c)$K[$c]=true;return
array_intersect_key($K,$l);}function
where_check($X,array$l=array()){parse_str($X,$eb);remove_slashes(array(&$eb));return
where($eb,$l);}function
where_link($r,$d,$Y,$ah="="){$Xg=($Y!==null?$ah:"IS NULL");return"&where[$r][col]=".url_escape($d).($Xg!=first(adminer()->operators())?"&where[$r][op]=".url_escape($Xg):"")."&where[$r][val]=".url_escape($Y);}function
convert_fields(array$e,array$l,array$O=array()){$K="";foreach($e
as$x=>$X){if($O&&!in_array(idf_escape($x),$O))continue;$Aa=convert_field($l[$x]);if($Aa)$K
.=", $Aa AS ".idf_escape($x);}return$K;}function
cookie_path(){return
strtr(preg_replace('~\?.*~','',$_SERVER["REQUEST_URI"]),array(";"=>"%3B",","=>"%2C"));}function
cookie($C,$Y,$Bf=2592000){header("Set-Cookie: $C=".rawurlencode($Y).($Bf?"; expires=".gmdate("D, d M Y H:i:s",time()+$Bf)." GMT":"")."; path=".cookie_path().(HTTPS?"; secure":"").($C=="adminer_import"?"":"; HttpOnly")."; SameSite=lax",false);}function
get_url($hl,$Fb){$http_response_header=null;$Yc=array();set_error_handler(function($Xc,$j)use(&$Yc){$Yc[]=preg_replace('~^file_get_contents\([^)]*\):\s*~','',$j);return
true;});$K=file_get_contents($hl,false,$Fb);restore_error_handler();$le=(function_exists('http_get_last_response_headers')?http_get_last_response_headers():$http_response_header);return
array($K,(preg_match('~^HTTP/[\d.]+ (\d+)~',idx($le,0,''),$B)?$B[1]:''),(array)$le,($K===false?implode("\n",$Yc):''),);}function
json_decode_exact($gf){$gf=preg_replace('~"(\\\\u0001(?:[^"\\\\]|\\\\.)*+")|"(?:[^"\\\\]|\\\\.)*+"(*SKIP)(*FAIL)~','"\\\\u0001$1',$gf);return
json_decode(preg_replace('~"(?:[^"\\\\]|\\\\.)*+"(*SKIP)(*FAIL)|-?\d[-+.\deE]*+~','"\\\\u0001$0"',$gf));}function
json_scalar($X){return(is_string($X)&&substr($X,0,1)=="\1"?substr($X,1):$X);}function
json_encode_exact($X,$Bd=0){return
preg_replace('~"\\\\u0001(-?\d[^"\\\\]*)"|(")\\\\u0001(\\\\u0001(?:[^"\\\\]|\\\\.)*+")|"(?:[^"\\\\]|\\\\.)*+"(*SKIP)(*FAIL)~','$1$2$3',json_encode($X,$Bd));}function
get_settings($Ib){parse_str($_COOKIE[$Ib],$nj);return$nj;}function
get_setting($x,$Ib="adminer_settings",$i=null){return
idx(get_settings($Ib),$x,$i);}function
save_settings(array$nj,$Ib="adminer_settings"){$Y=http_build_query($nj+get_settings($Ib));cookie($Ib,$Y);$_COOKIE[$Ib]=$Y;}function
restart_session(){if(!ini_bool("session.use_cookies")&&(!function_exists('session_status')||session_status()==PHP_SESSION_NONE))session_start();}function
stop_session($Ed=false){$kl=ini_bool("session.use_cookies");if(!$kl||$Ed){session_write_close();if($kl&&ini_set("session.use_cookies",'0')===false)session_start();}}function&get_session($x){return$_SESSION[$x][DRIVER][SERVER][$_GET["username"]];}function
set_session($x,$X){$_SESSION[$x][DRIVER][SERVER][$_GET["username"]]=$X;}function
auth_url($wl,$P,$ol,$h=null){$gl=remove_from_uri(implode("|",array_keys(SqlDriver::$drivers))."|username|ext|".($h!==null?"db|":"").($wl=='mssql'||$wl=='pgsql'?"":"ns|").session_name());preg_match('~([^?]*)\??(.*)~',$gl,$B);return"$B[1]?".(sid()?SID."&":"").($_GET["ext"]?"ext=".url_escape($_GET["ext"])."&":"").($wl!="server"||$P!=""?url_escape($wl)."=".url_escape($P)."&":"")."username=".url_escape($ol).($h!=""?"&db=".url_escape($h):"").($B[2]?"&$B[2]":"");}function
is_ajax(){return($_SERVER["HTTP_X_REQUESTED_WITH"]=="XMLHttpRequest");}function
redirect($A,$eg=null){if($eg!==null){restart_session();$_SESSION["messages"][preg_replace('~^[^?]*~','',($A!==null?$A:$_SERVER["REQUEST_URI"]))][]=$eg;}if($A!==null){if($A=="")$A=".";header("Location: $A");exit;}}function
query_redirect($I,$A,$eg,$ti=true,$ed=true,$od=false,$sk=""){if($ed){$Dj=microtime(true);$od=!connection()->query($I);$sk=format_time($Dj);}$zj=($I?adminer()->messageQuery($I,$sk,$od):"");if($od){adminer()->error
.=adminer()->error().$zj.script("messagesPrint();")."<br>";return
false;}if($ti)redirect($A,$eg.$zj);return
true;}class
Queries{static$queries=array();static$start=0;}function
remember_query($I){if(!Queries::$start)Queries::$start=microtime(true);Queries::$queries[]=(driver()->delimiter!=';'?$I:(preg_match('~;$~',$I)?"DELIMITER ;;\n$I;\nDELIMITER ":$I).";");}function
queries($I){remember_query($I);return
connection()->query($I);}function
apply_queries($I,array$U,$ad='Adminer\table'){foreach($U
as$S){if(!queries("$I ".$ad($S)))return
false;}return
true;}function
queries_redirect($A,$eg,$ti){$oi=implode("\n",Queries::$queries);$sk=format_time(Queries::$start);return
query_redirect($oi,$A,$eg,$ti,false,!$ti,$sk);}function
format_time($Dj){return
lang(2,max(0,microtime(true)-$Dj));}function
relative_uri($gl=''){return
preg_replace_callback('~^[^?]*~',function($B){return
str_replace(":","%3A",$B[0]);},preg_replace('~^[^?]*/([^?]*)~','\1',($gl?:$_SERVER["REQUEST_URI"])));}function
remove_from_uri($xh=""){return
substr(preg_replace("~(?<=[?&])($xh".(SID?"":"|".session_name()).")=[^&]*&~",'',relative_uri()."&"),0,-1);}function
get_files($C,$cc=false){$vd=$_FILES[$C];if(!$vd)return
null;foreach($vd
as$x=>$X)$vd[$x]=(array)$X;$K=array();foreach($vd["error"]as$x=>$j){if($j)return$j;$m=$vd["name"][$x];$_k=$vd["tmp_name"][$x];$Db=file_get_contents($cc&&preg_match('~\.gz$~',$m)?"compress.zlib://$_k":$_k);if($cc){$Dj=substr($Db,0,3);if(function_exists("iconv")&&preg_match("~^\xFE\xFF|^\xFF\xFE~",$Dj))$Db=iconv("utf-16","utf-8",$Db);elseif($Dj=="\xEF\xBB\xBF")$Db=substr($Db,3);}$K[]=array($m,$Db);}return$K;}function
get_file($x,$cc=false,$ic=""){$yd=get_files($x,$cc);if(!is_array($yd))return$yd;$K='';foreach($yd
as$vd){$Db=$vd[1];$K
.=$Db;if($ic)$K
.=(preg_match("($ic\\s*\$)",$Db)?"":$ic)."\n\n";}return$K;}function
upload_error($j){$Wf=($j==UPLOAD_ERR_INI_SIZE?ini_get("upload_max_filesize"):0);return($j?lang(3).($Wf?" ".lang(4,$Wf):""):lang(5));}function
is_utf8($X){return(preg_match('~~u',$X)&&!preg_match('~[\0-\x8\xB\xC\xE-\x1F]~',$X));}function
utf8_length($X){return
strlen(preg_replace('~[\x80-\xBF]~','',$X));}function
format_number($X){preg_match('~^#+([^#0]+)(?:(#+)\1)?(#*0)$~u',lang(6),$B);$rj=strlen($B[3]);$K=number_format($X,0,".","");$K=preg_replace('~\B(?=(\d{'.(strlen($B[2])?:$rj).'})*\d{'.$rj.'}$)~',$B[1],$K);return
strtr($K,preg_split('~~u',lang(7),-1,PREG_SPLIT_NO_EMPTY));}function
format_status(array$T,$x){$X=idx($T,$x,'?');if(!is_numeric($X))return
h($X);if($X<0)return'?';$wa=($x=="Rows"&&(JUSH=="sqlite"||$T["Engine"]==(JUSH=="pgsql"?"table":"InnoDB")));return($wa?"~ ":"").format_number($X);}function
friendly_url($X){return
preg_replace('~\W~i','-',$X);}function
table_status1($S,$pd=false){$K=table_status($S,$pd);return($K?reset($K):array("Name"=>$S));}function
column_foreign_keys($S){$K=array();foreach(adminer()->foreignKeys($S)as$n){foreach($n["source"]as$X)$K[$X][]=$n;}return$K;}function
fields_from_edit(){$K=array();foreach((array)$_POST["field_keys"]as$x=>$X){if($X!=""){$X=bracket_escape($X);$_POST["function"][$X]=$_POST["field_funs"][$x];$_POST["fields"][$X]=$_POST["field_vals"][$x];}}foreach((array)$_POST["fields"]as$x=>$X){$C=bracket_escape($x,true);$K[$C]=array("field"=>$C,"full_type"=>"","type"=>"","privileges"=>array("insert"=>1,"update"=>1,"where"=>1,"order"=>1),"null"=>true,"auto_increment"=>($C==driver()->primary),);}return$K;}function
dump_headers($ze,$vg=false){$K=adminer()->dumpHeaders($ze,$vg);$th=$_POST["output"];if($th!="text"||$K=="tar"){$xb=($th!="text"&&$th!="file"&&preg_match('~^[0-9a-z]+$~',$th)?".$th":"");header("Content-Disposition: attachment; filename=".adminer()->dumpFilename($ze).".$K$xb");}session_write_close();if(!ob_get_level())ob_start(null,4096);ob_flush();flush();return$K;}function
dump_csv(array$L){$Ok=$_POST["format"]=="tsv";foreach($L
as$x=>$X){if(preg_match('~["\n]|^0[^.]|\.\d*0$|'.($Ok?'\t':'[,;]|^$').'~',$X))$L[$x]='"'.str_replace('"','""',$X).'"';}echo
implode(($_POST["format"]=="csv"?",":($Ok?"\t":";")),$L)."\r\n";}function
parse_csv($Qb,$dj){$K=array();preg_match_all('~(?>"[^"]*"|[^"\r\n]+)+~',$Qb,$Mf);foreach($Mf[0]as$L){preg_match_all("~((?>\"[^\"]*\")+|[^$dj]*)$dj~",$L.$dj,$Nf);$K[]=$Nf[1];}return$K;}function
csv_value($X){return(preg_match('~^".*"$~s',$X)?str_replace('""','"',substr($X,1,-1)):$X);}function
apply_sql_function($p,$d){return($p?($p=="unixepoch"?"DATETIME($d, '$p')":($p=="count distinct"?"COUNT(DISTINCT ":strtoupper("$p("))."$d)"):$d);}function
get_temp_dir(){return
ini_get("upload_tmp_dir")?:sys_get_temp_dir();}function
file_open_lock($m){if(is_link($m))return;$o=@fopen($m,"c+");if(!$o)return;@chmod($m,0660);if(!flock($o,LOCK_EX)){fclose($o);return;}return$o;}function
file_write_unlock($o,$Ub){rewind($o);fwrite($o,$Ub);ftruncate($o,strlen($Ub));file_unlock($o);}function
file_unlock($o){flock($o,LOCK_UN);fclose($o);}function
first(array$_a){return
reset($_a);}function
password_file($Lb){$m=get_temp_dir()."/adminer.key";if(!$Lb&&!file_exists($m))return'';$o=file_open_lock($m);if(!$o)return'';$K=stream_get_contents($o);if(!$K){$K=rand_string();file_write_unlock($o,$K);}else
file_unlock($o);return$K;}function
rand_string(){return(function_exists('random_bytes')?bin2hex(random_bytes(16)):md5(uniqid(strval(mt_rand()),true)));}function
select_value($X,$_,array$k,$qk,array$Nh=array()){if(is_array($X)){$K="";if(array_filter($X,'is_array')==array_values($X)){$lf=array();foreach($X
as$W)$lf+=array_fill_keys(array_keys($W),null);foreach(array_keys($lf)as$if)$K
.="<th>".h($if);foreach($X
as$W){$K
.="<tr>";foreach(array_merge($lf,$W)as$ql)$K
.="<td>".select_value($ql,$_,$k,$qk,$Nh);}}else{foreach($X
as$if=>$W)$K
.="<tr>".($X!=array_values($X)?"<th>".h($if):"")."<td>".select_value($W,$_,$k,$qk,$Nh);}return"<table>$K</table>";}if(!$_)$_=adminer()->selectLink($X,$k);if($_===null){if(is_mail($X))$_="mailto:$X";if(is_url($X))$_=$X;}$X=driver()->value($X,$k);$K=adminer()->editVal($X,$k);if($K!==null){if(!is_utf8($K))$K="\0";elseif($qk!=""&&is_shortable($k))$K=shorten_utf8($K,max(0,+$qk),"",$Nh);else$K=highlight_matches($K,$Nh);}return
adminer()->selectVal($K,$_,$k,$X);}function
is_blob(array$k){return
preg_match('~blob|bytea|raw|file'.(JUSH=="mssql"?'|binary|image':'').'~',$k["type"])&&!in_array($k["type"],idx(driver()->structuredTypes(),lang(0),array()));}function
is_identity_always(array$k){return$k["auto_increment"]&&(JUSH=="mssql"||$k["default"]=="GENERATED ALWAYS AS IDENTITY");}function
is_mail($Nc){$Ca='[-a-z0-9!#$%&\'*+/=?^_`{|}~]';$_c='[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])';$Mh="$Ca+(\\.$Ca+)*@($_c?\\.)+$_c";return
is_string($Nc)&&preg_match("(^$Mh(,\\s*$Mh)*\$)i",$Nc);}function
is_url($R){$_c='[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])';return
preg_match("~^((https?):)?//($_c?\\.)+$_c(:\\d+)?(/.*)?(\\?.*)?(#.*)?\$~i",$R);}function
is_ipv6($ia){$q='[\da-f]{1,4}';$Ze='\d{1,3}(\.\d{1,3}){3}';return(bool)preg_match("~^(($q:){7}$q|($q:){6}$Ze|(($q:)*$q)?::(($q:)*($q|$Ze))?)$~iD",$ia);}function
is_shortable(array$k){return!preg_match('~'.number_type().'|date|time|year~',$k["type"]);}function
url_host($ve){return(strpos($ve,":")!==false?"[$ve]":$ve);}function
server_parts(array$Hh){return
array("scheme"=>(string)$Hh["scheme"],"host"=>(string)$Hh["host"],"port"=>(string)$Hh["port"],"socket"=>(string)$Hh["socket"],"path"=>(string)$Hh["path"],);}function
parse_server($P){if($P=="")return
server_parts(array());if($P[0]==":"&&!is_ipv6($P)){$Ei=substr($P,1);if(preg_match('~^\d+$~D',$Ei))return
server_parts(array("port"=>$Ei));return(preg_match('~^/[-\w.:/]*$~D',$Ei)?server_parts(array("socket"=>$Ei)):null);}$Si="";if(preg_match('~^([-+.\w]+)://~',$P,$B)){$Si=strtolower($B[1]);$P=substr($P,strlen($B[0]));}if(preg_match('~^\[(.+)](:(\d+))?(/[-\w./]*)?$~D',$P,$B))return(is_ipv6($B[1])?server_parts(array("scheme"=>$Si,"host"=>$B[1],"port"=>$B[3],"path"=>$B[4])):null);if(is_ipv6($P))return
server_parts(array("scheme"=>$Si,"host"=>$P));if(preg_match('~^(/[-\w./]*)(:(\d+))?$~D',$P,$B))return
server_parts(array("scheme"=>$Si,"host"=>$B[1],"port"=>$B[3]));return(preg_match('~^([-\w.]*)(:(\d+))?(/[-\w./]*)?$~D',$P,$B)?server_parts(array("scheme"=>$Si,"host"=>$B[1],"port"=>$B[3],"path"=>$B[4])):null);}function
count_rows($S,array$Z,$bf,array$q){$I=" FROM ".table($S).($Z?" WHERE ".implode(" AND ",$Z):"");return($bf&&(JUSH=="sql"||count($q)==1)?"SELECT COUNT(DISTINCT ".implode(", ",$q).")$I":"SELECT COUNT(*)".($bf?" FROM (SELECT 1$I GROUP BY ".implode(", ",$q).") x":$I));}function
slow_query($I){$h=adminer()->database();$tk=adminer()->queryTimeout();$sj=driver()->slowQuery($I,$tk);$f=null;if(!$sj&&support("kill")){$f=connect();if($f&&($h==""||$f->select_db($h))){$mf=number(get_val(connection_id(),0,$f));echo
script("const timeout = setTimeout(() => { ajax('".js_escape(ME)."script=kill', function () {}, 'kill=$mf&token=".get_token()."'); }, 1000 * $tk);");}}ob_flush();flush();$K=@get_key_vals(($sj?:$I),$f,false);if($f){echo
script("clearTimeout(timeout);");ob_flush();flush();}return$K;}function
get_token(){$ri=rand(1,1e6);return($ri^$_SESSION["token"]).":$ri";}function
verify_token(){list($Ak,$ri)=explode(":",$_POST["token"]);return($ri^$_SESSION["token"])==$Ak&&in_array($_SERVER["HTTP_SEC_FETCH_SITE"],array("","same-origin"));}function
compress_alphabet(){return
strtr(implode(range('"','~')),"'\\","!\n");}function
decompress_string($R,$oc=""){$sa=array_flip(str_split(compress_alphabet()));$y=strlen($R);$sl=($y?13*($y-1)/2-$sa[$R[0]]:0);$Ra="";$Ei=0;$Fi=0;for($r=1;$r<$y;$r+=2){$Ei=($Ei<<13)+$sa[$R[$r]]*93+$sa[$R[$r+1]];$Fi+=13;while($Fi>=8&&$sl>=8){$Fi-=8;$sl-=8;$Ra
.=chr($Ei>>$Fi);$Ei&=(1<<$Fi)-1;}}if($Ra=="")return"";if($oc!=""&&function_exists('inflate_init'))return
inflate_add(inflate_init(ZLIB_ENCODING_RAW,array('dictionary'=>$oc)),$Ra,ZLIB_FINISH);return($oc==""&&function_exists('gzinflate')?gzinflate($Ra):inflate($Ra,$oc));}function
inflate($Ra,$oc=""){$zf=array(3,4,5,6,7,8,9,10,11,13,15,17,19,23,27,31,35,43,51,59,67,83,99,115,131,163,195,227,258);$_f=array(0,0,0,0,0,0,0,0,1,1,1,1,2,2,2,2,3,3,3,3,4,4,4,4,5,5,5,5,0);$sc=array(1,2,3,4,5,7,9,13,17,25,33,49,65,97,129,193,257,385,513,769,1025,1537,2049,3073,4097,6145,8193,12289,16385,24577);$uc=array(0,0,0,0,1,1,2,2,3,3,4,4,5,5,6,6,7,7,8,8,9,9,10,10,11,11,12,12,13,13);$K=$oc;$H=0;do{$_d=inflate_bits($Ra,$H,1);$V=inflate_bits($Ra,$H,2);if(!$V){$H=($H+7)&~7;$y=inflate_bits($Ra,$H,16);$H+=16;$K
.=substr($Ra,$H>>3,$y);$H+=$y<<3;}else{if($V==1){$Hf=array_merge(array_fill(0,144,8),array_fill(0,112,9),array_fill(0,24,7),array_fill(0,8,8));$vc=array_fill(0,30,5);}else{$Gf=inflate_bits($Ra,$H,5)+257;$tc=inflate_bits($Ra,$H,5)+1;$E=array(16,17,18,0,8,7,9,6,10,5,11,4,12,3,13,2,14,1,15);$kg=array_fill(0,19,0);$jg=inflate_bits($Ra,$H,4)+4;for($r=0;$r<$jg;$r++)$kg[$E[$r]]=inflate_bits($Ra,$H,3);$lg=inflate_table($kg);$Af=array();while(count($Af)<$Gf+$tc){$Rj=inflate_symbol($Ra,$H,$lg);if($Rj==16)$Af=array_merge($Af,array_fill(0,inflate_bits($Ra,$H,2)+3,end($Af)));elseif($Rj==17)$Af=array_merge($Af,array_fill(0,inflate_bits($Ra,$H,3)+3,0));elseif($Rj==18)$Af=array_merge($Af,array_fill(0,inflate_bits($Ra,$H,7)+11,0));else$Af[]=$Rj;}$Hf=array_slice($Af,0,$Gf);$vc=array_slice($Af,$Gf);}$If=inflate_table($Hf);$xc=inflate_table($vc);while(($Rj=inflate_symbol($Ra,$H,$If))!=256){if($Rj<256)$K
.=chr($Rj);else{$y=$zf[$Rj-257]+inflate_bits($Ra,$H,$_f[$Rj-257]);$wc=inflate_symbol($Ra,$H,$xc);$Sg=strlen($K)-$sc[$wc]-inflate_bits($Ra,$H,$uc[$wc]);for($r=0;$r<$y;$r++)$K
.=$K[$Sg+$r];}}}}while(!$_d);return($oc==""?$K:substr($K,strlen($oc)));}function
inflate_bits($Ra,&$H,$Kb){$K=0;for($r=0;$r<$Kb;$r++){$K+=((ord($Ra[$H>>3])>>($H&7))&1)<<$r;$H++;}return$K;}function
inflate_table(array$Af){$S=array();$nb=0;for($Sa=1;$Sa<=max($Af);$Sa++){foreach($Af
as$Rj=>$y){if($y==$Sa){$S[$Sa][$nb]=$Rj;$nb++;}}$nb<<=1;}return$S;}function
inflate_symbol($Ra,&$H,array$S){$nb=0;$Sa=0;do{$nb=($nb<<1)+inflate_bits($Ra,$H,1);$Sa++;}while(!isset($S[$Sa][$nb]));return$S[$Sa][$nb];}function
script($wj,$Dk="\n"){return"<script".nonce().">$wj</script>$Dk";}function
script_src($hl,$fc=false){return"<script src='".h($hl)."'".nonce().($fc?" defer":"")."></script>\n";}function
nonce(){return' nonce="'.get_nonce().'"';}function
on($bd,$de,$xa=null){$za=array();foreach(array_slice(func_get_args(),2)as$X)$za[]=json_encode($X,256);return" data-on$bd='".str_replace(array('&','<',"'"),array('&amp;','&lt;','&#039;'),"$de(".implode(", ",$za).")")."'";}function
input_hidden($C,$Y=""){return"<input type='hidden' name='".h($C)."' value='".h($Y)."'>\n";}function
input_token(){return
input_hidden("token",get_token());}function
target_blank(){return' target="_blank" rel="noreferrer noopener"';}function
h($R){return
str_replace(array('&','<','"',"'","\0"),array('&amp;','&lt;','&quot;','&#039;','&#0;'),$R);}function
nl_br($R){return
str_replace("\n","<br>",$R);}function
checkbox($C,$Y,$gb,$nf="",$b="",$mb="",$pf=""){$K="<input type='checkbox' name='$C' value='".h($Y)."'".($gb?" checked":"").($nf==""&&$mb?" class='$mb'":"").($pf?" aria-labelledby='$pf'":"").$b.">";return($nf!=""?"<label".($mb?" class='$mb'":"").">$K".h($nf)."</label>":$K);}function
optionlist($eh,$aj=null,$ll=false){$K="";foreach($eh
as$if=>$W){$fh=array($if=>$W);if(is_array($W)){$K
.='<optgroup label="'.h($if).'">';$fh=$W;}foreach($fh
as$x=>$X)$K
.='<option'.($ll||is_string($x)?' value="'.h($x).'"':'').($aj!==null&&($ll||is_string($x)?(string)$x:$X)===$aj?' selected':'').'>'.h($X);if(is_array($W))$K
.='</optgroup>';}return$K;}function
group_system(array$Cg,$Ri=false){$K=array();$Sj=array();foreach($Cg
as$C){if($Ri?driver()->isSystem(DB,$C):driver()->isSystem($C))$Sj[]=$C;else$K[]=$C;}if($Sj)$K[lang(8,'')]=$Sj;return$K;}function
html_select($C,array$eh,$Y="",$b="",$pf=""){static$nf=0;$of="";if(!$pf&&substr($eh[""],0,1)=="("){$nf++;$pf="label-$nf";$of="<option value='' id='$pf'>".h($eh[""]);unset($eh[""]);}return"<select name='".h($C)."'".($pf?" aria-labelledby='$pf'":"")."$b>".$of.optionlist($eh,$Y)."</select>";}function
html_radios($C,array$eh,$Y="",$dj=""){$K="";foreach($eh
as$x=>$X)$K
.="<label><input type='radio' name='".h($C)."' value='".h($x)."'".($x==$Y?" checked":"").">".h($X)."</label>$dj";return$K;}function
confirm($eg=""){return
on('click','confirmClick',$eg?:lang(9));}function
print_fieldset($s,$yf,$_l=false){echo"<fieldset><legend>","<a href='#fieldset-$s' class='toggle'>$yf</a>","</legend>","<div id='fieldset-$s'".($_l?"":" class='hidden'").">\n";}function
bold($Ta,$mb=""){return($Ta?" class='active $mb'":($mb?" class='$mb'":""));}function
js_escape($R){return
str_replace("<","\\x3C",addcslashes($R,"\r\n'\\"));}function
js_escape_re($R){return
addcslashes(preg_quote($R,"/"),"\r\n");}function
pagination_href($F){return
remove_from_uri("page|next").($F?"&page=$F".($_GET["next"]!=""?"&next=".url_escape($_GET["next"]):""):"");}function
pagination($F,$Rb){return" ".($F==$Rb?($F?"<b>".($F+1)."</b>":$F+1):'<a href="'.h(pagination_href($F)).'">'.($F+1)."</a>");}function
hidden_fields(array$ki,array$Ce=array(),$ci=''){$K=false;foreach($ki
as$x=>$X){if(!in_array($x,$Ce)){if(is_array($X))hidden_fields($X,array(),$x);else{$K=true;echo
input_hidden(($ci?$ci."[$x]":$x),$X);}}}return$K;}function
hidden_fields_get(){echo(sid()?input_hidden(session_name(),session_id()):''),($_GET["ext"]?input_hidden("ext",$_GET["ext"]):""),(isset($_GET[DRIVER])?input_hidden(DRIVER,SERVER):""),input_hidden("username",$_GET["username"]);}function
on_upload_progress(&$fl){$fl=(ini_bool("session.upload_progress.enabled")&&ini_get("session.upload_progress.name")?rand_string():"");return($fl?on('submit','uploadProgress',ME."upload=$fl",SESSION_NAME."=$fl"):"");}function
file_input($b,$Ei=""){$Qf="max_file_uploads";$Rf=ini_get($Qf);$Wf="upload_max_filesize";$Xf=ini_bytes($Wf);$Yh=ini_bytes("post_max_size");if($Yh&&$Yh<$Xf){$Wf="post_max_size";$Xf=$Yh;}$Yf=ini_get($Wf);return(ini_bool("file_uploads")?"<input type='file'$b".on('change','fileChange',(int)$Rf,lang(10,"$Qf = $Rf"),$Xf,lang(10,"$Wf = $Yf")).">$Ei":lang(11));}function
enum_input($V,$b,array$k,$Y,$Qc=""){preg_match_all("~".driver()->enumLength."~",$k["length"],$Mf);$ci=($k["type"]=="enum"?"val-":"");$gb=(is_array($Y)?in_array("null",$Y):$Y===null);$K=($k["null"]&&$ci?"<label><input type='$V'$b value='null'".($gb?" checked":"")."><i>$Qc</i></label>":"");foreach($Mf[0]as$X){$X=stripcslashes(idf_unescape($X));$gb=(is_array($Y)?in_array($ci.$X,$Y):$Y===$X);$K
.=" <label><input type='$V'$b value='".h($ci.$X)."'".($gb?' checked':'').'>'.h(adminer()->editVal($X,$k)).'</label>';}return$K;}function
input(array$k,$Y,$p,$Ia=false,$cl=false){$C=h(bracket_escape($k["field"]));echo"<td class='function'>";$Wc=driver()->enumLength($k);if($Wc){$k["type"]="enum";$k["length"]=$Wc;}$eh=($k["type"]=="enum"||$k["type"]=="set");if(is_array($Y)&&!$p&&!$eh)$p="json";$gf=($p=="json"||preg_match('~^jsonb?$~',$k["full_type"]));if($gf&&$Y!=''&&(JUSH!="pgsql"||$k["type"]!="json")&&(is_array($Y)||!$_POST["save"]))$Y=(is_array($Y)?json_encode($Y,128|64|256):json_encode_exact(json_decode_exact($Y),128|64|256));$Di=($cl&&is_identity_always($k));if($Di&&!$_POST["save"])$p=null;$Sd=(isset($_GET["select"])||$Di?array("orig"=>lang(12)):array())+adminer()->editFunctions($k);$b=" name='fields[$C]".($eh?"[]":"")."'".($Ia?" autofocus":"");echo
driver()->unconvertFunction($k)." ";$S=$_GET["edit"]?:$_GET["select"];if($k["type"]=="enum")echo
h($Sd[""])."<td>".adminer()->editInput($S,$k,$b,$Y);else{$fe=(in_array($p,$Sd)||isset($Sd[$p]));$Ad=0;foreach($Sd
as$x=>$X){if($x===""||!$X)break;$Ad++;}echo(count($Sd)>1?"<select name='function[$C]'".on('change','functionChange').on_help_value('^SQL$').">".optionlist($Sd,$p===null||$fe?$p:"")."</select>":h(reset($Sd)))."<td".($Ad&&count($Sd)>1?on('input','skipOriginal',$Ad):"").">";$Qe=adminer()->editInput($S,$k,$b,$Y);if($Qe!="")echo$Qe;elseif(preg_match('~bool~',$k["type"]))echo"<input type='hidden'$b value='0'>"."<input type='checkbox'".(preg_match('~^(1|t|true|y|yes|on)$~i',$Y)?" checked":"")."$b value='1'>";elseif($k["type"]=="set")echo
enum_input("checkbox",$b,$k,(is_string($Y)?explode(",",$Y):$Y));elseif(is_blob($k)&&ini_bool("file_uploads"))echo"<input type='file' name='fields-$C'>";elseif($gf)echo"<textarea$b cols='50' rows='12' class='jush-json'>".h($Y).'</textarea>';elseif(($pk=preg_match('~text|lob|memo~i',$k["type"]))||preg_match("~\n~",$Y)){if($pk&&JUSH!="sqlite")$b
.=" cols='50' rows='12'";else{$M=min(12,substr_count($Y,"\n")+1);$b
.=" cols='30' rows='$M'";}echo"<textarea$b>".h($Y).'</textarea>';}else{$Sk=driver()->types();$Qk=$Sk[$k["type"]];$_a=preg_match('~\[]~',$k["full_type"]);if($_a)$Zf=0;elseif(preg_match('~date|time|year~',$k["type"])){$bi=($k["length"]==""&&JUSH=="pgsql"?6:$k["length"]);$Ld=(preg_match('~time~',$k["type"])&&preg_match('~^[1-9]\d*$~',$bi)?$bi+1:0);$Zf=($Qk?$Qk+$Ld:0);}elseif(!preg_match('~int|vector~',$k["type"])&&preg_match('~^(\d+)(,(\d+))?$~',$k["length"],$B))$Zf=(preg_match("~binary~",$k["type"])?2:1)*$B[1]+($B[3]?1:0)+($B[2]&&!$k["unsigned"]?1:0);else$Zf=($Qk?$Qk+($k["unsigned"]?0:1):0);echo"<input".((!$fe||$p==="")&&preg_match('~^'.int_type().'$~',$k["type"])&&!$_a?" type='number'":"")." value='".h($Y)."'".($Zf?" data-maxlength='$Zf'":"").(preg_match('~char|binary~',$k["type"])&&$Zf>20?" size='".($Zf>99?60:40)."'":"")."$b>";}echo
adminer()->editHint($S,$k,$Y),(count($Sd)>1?script("fire(qs('select', qsl('td').previousSibling), 'change');",""):"");}}function
process_input(array$k){$t=bracket_escape($k["field"]);$p=idx($_POST["function"],$t);if($p=="orig")return(preg_match('~^CURRENT_TIMESTAMP~i',$k["on_update"])?idf_escape($k["field"]):false);if($p=="NULL")return"NULL";if(is_blob($k)&&ini_bool("file_uploads")){$vd=get_file("fields-$t");if(!is_string($vd))return
false;return
driver()->quoteBinary($vd);}$Y=idx($_POST["fields"],$t);if($Y===null)return
false;if($k["type"]=="enum"||driver()->enumLength($k)){$Y=idx($Y,0);if($Y=="orig"||!$Y)return
false;if($Y=="null")return"NULL";$Y=substr($Y,4);}if($k["auto_increment"]&&$Y=="")return
null;if($k["type"]=="set")$Y=implode(",",(array)$Y);if($p=="json"){$Y=json_decode($Y,true);if(!is_array($Y))return
false;return$Y;}return
adminer()->processInput($k,$Y,$p);}function
search_tables(){$_GET["where"][0]["val"]=$_POST["query"];$cj="<ul>\n";foreach(table_status('',true)as$S=>$T){$C=adminer()->tableName($T);if(isset($T["Engine"])&&$C!=""&&(!$_POST["tables"]||in_array($S,$_POST["tables"]))){$J=connection()->query("SELECT".limit("1 FROM ".table($S)," WHERE ".implode(" AND ",adminer()->selectSearchProcess(fields($S),array(),$T)),1));if(!$J||$J->fetch_row()){$hi="<a href='".h(ME."select=".url_escape($S)."&where[0][op]=".url_escape($_GET["where"][0]["op"])."&where[0][val]=".url_escape($_GET["where"][0]["val"]))."'>$C</a>";echo"$cj<li>".($J?$hi:"<p class='error'>$hi: ".adminer()->error())."\n";$cj="";}}}echo($cj?"<p class='message'>".lang(13):"</ul>")."\n";}function
on_help($pk,$qj=0){return
on('mouseover','helpMouseover',$pk,$qj).on('mouseout','helpMouseout');}function
on_help_value($_i="",$Ci=""){return
on('mouseover','helpValueMouseover',$_i,$Ci).on('mouseout','helpMouseout');}function
edit_form($S,array$l,$L,$cl,$j='',$I='',$sk=''){$Yj=adminer()->tableName(table_status1($S,true));page_header(($cl?lang(14):lang(15)),$j,array("select"=>array($S,$Yj)),$Yj);adminer()->editRowPrint($S,$l,$L,$cl,$I,$sk);if($L===false){echo"<p class='error'>".lang(16)."\n";return;}echo"<form action='' method='post' enctype='multipart/form-data' id='form'>\n";$Lc=false;$Fl=($cl&&!isset($_GET["select"])?where_columns($l):array());$Gb=(count($Fl)!=count($l));if(!$Gb)$Fl=array();if(!$l)echo"<p class='error'>".lang(17)."\n";else{echo"<table class='layout nowrap'".on('keydown','editingKeydown').">\n";$Ia=!$_POST;foreach($l
as$C=>$k){echo"<tr".($Fl[$C]?on('change','whereChange'):"")."><th>".adminer()->fieldName($k);$i=idx($_GET["set"],bracket_escape($C));if($i===null){$i=$k["default"];if($k["type"]=="bit"&&preg_match("~^b'([01]*)'\$~",$i,$Bi))$i=$Bi[1];if(JUSH=="sql"&&preg_match('~binary~',$k["type"]))$i=bin2hex($i);}$Y=($L!==null?($k["type"]=="set"&&is_array($L[$C])?implode(",",$L[$C]):(is_bool($L[$C])?+$L[$C]:$L[$C])):(!$cl&&$k["auto_increment"]?"":(isset($_GET["select"])?false:$i)));if(!$_POST["save"]&&is_string($Y))$Y=adminer()->editVal($Y,$k);if(($cl&&!isset($k["privileges"]["update"]))||$k["generated"])echo"<td class='function'><td>".select_value($Y,'',$k,null);else{$Lc=true;$p=($_POST["save"]?idx($_POST["function"],bracket_escape($C),""):($cl&&preg_match('~^CURRENT_TIMESTAMP~i',$k["on_update"])?"now":($Y===false?null:($Y!==null?'':'NULL'))));if(!$_POST&&!$cl&&$Y==$k["default"]&&preg_match('~^[\w.]+\(~',$Y))$p="SQL";if(preg_match("~time~",$k["type"])&&preg_match('~^CURRENT_TIMESTAMP~i',$Y)){$Y="";$p="now";}if($k["type"]=="uuid"&&$Y=="uuid()"){$Y="";$p="uuid";}if($Ia!==false)$Ia=($k["auto_increment"]||$p=="now"||$p=="uuid"?null:true);input($k,$Y,$p,$Ia,$cl);if($Ia)$Ia=false;}}if(!fields($S)&&driver()->primary!="")echo"<tr>"."<th><input name='field_keys[]'".on('input','fieldChange').">"."<td class='function'>".html_select("field_funs[]",adminer()->editFunctions(array("null"=>isset($_GET["select"]))))."<td><input name='field_vals[]'>";echo"</table>\n";}echo"<p>\n";if($Lc){echo"<input type='submit' value='".lang(18)."'>\n";if(!isset($_GET["select"])&&$Gb){$pc=($Fl&&($j!=""||adminer()->error!="")?" disabled":"");echo"<input type='submit' name='insert' value='".($cl?lang(19):lang(20))."' title='Ctrl+Shift+Enter'$pc".($cl?on('click','ajaxForm',lang(21)):"").">\n";}}echo($cl?"<input type='submit' name='delete' value='".lang(22)."'".confirm().">\n":"");if(isset($_GET["select"]))hidden_fields(array("check"=>(array)$_POST["check"],"clone"=>$_POST["clone"],"all"=>$_POST["all"]));echo
input_hidden("referer",(isset($_POST["referer"])?$_POST["referer"]:$_SERVER["HTTP_REFERER"])),input_hidden("save",1),input_token(),"</form>\n";}function
repeat_pattern($Mh,$y){return
str_repeat("$Mh{0,65535}",$y/65535)."$Mh{0,".($y%65535)."}";}function
shorten_utf8($R,$y=80,$Nj="",array$Nh=array()){if(!preg_match("(^(".repeat_pattern("[\t\r\n -\x{10FFFF}]",$y).")($)?)u",$R,$B))preg_match("(^(".repeat_pattern("[\t\r\n -~]",$y).")($)?)",$R,$B);$y=strlen(isset($B[2])?$B[1]:preg_replace('~\n[^\n]*\z~',"\n",$B[1]));return
highlight_matches($R,$Nh,$y).$Nj.(isset($B[2])?"":"<i>…</i>");}function
highlight_matches($R,array$Nh,$y=null){if($y===null)$y=strlen($R);$K="";$H=0;if($Nh&&@preg_match_all("((?|".implode("|",$Nh)."))su",$R,$Mf,PREG_OFFSET_CAPTURE)){foreach($Mf[0]as$B){list($pk,$Dj)=$B;if($pk!=""&&$Dj<$y){$Rc=min($Dj+strlen($pk),$y);$K
.=h(substr($R,$H,$Dj-$H))."<mark>".h(substr($R,$Dj,$Rc-$Dj))."</mark>";$H=$Rc;}}}return$K.h(substr($R,$H,$y-$H));}function
icon($ye,$C,$xe,$vk,$b=""){return"<button ".($C?"type='submit' name='$C'":"draggable='true' tabindex='-1'")." title='".h($vk)."' class='icon icon-$ye".($C?"":" jsonly")."'$b><span>$xe</span></button>";}function
copy_icon(){$Jb=lang(23);return"<a href='' class='jsonly icon-copy' title='$Jb'><span>$Jb</span></a>";}if(isset($_GET["file"])){if($_SERVER["HTTP_IF_MODIFIED_SINCE"]){header("HTTP/1.1 304 Not Modified");exit;}header("Expires: ".gmdate("D, d M Y H:i:s",time()+365*24*60*60)." GMT");header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");header("Cache-Control: immutable");ini_set("zlib.output_compression",'1');if($_GET["file"]=="default.css"){header("Content-Type: text/css; charset=utf-8");echo
decompress_string('+c(<]iDp;+<8]XG-X#ETBP{IAOo`HAD0Z,$t2FfTr3g#Vd(TVf>Cx5+d&ycL<,9"B6]b"oPK(+THBssK@e0=xnaBRf;$]A0]jsW_*Ibe$(2;yd5/}P:0_3xBmwvnkq<ydKh3e?rW3UW8c6iorbru~.;FOKSUq)z0u4OH&MRf7i:
N[U_7;F1p9i(5s29CE=`-:Ze`.kn@9RF4QL-+YuW|6!U&>e-zoVW6?4.A8t`/B@/ELQUIrIU%7Kvs3S?k#p2;1H
X/y@s?AR7M+0t;gg~I=j9:,1HW;ic+?$`BB#$=qSuRwY!<DX3Ny>D#NN*xh]"#vZCW/M>Y@mG3*h%?kSmN=OT;f?cTPrvH,vnUVam2HYmY(?/@
0?NF?2Ol^+5I
U%GE]1O0$>ys`u;eY*<H,E)jU7$
/G"I;uWG$9)5gsRW{:{e~U~.8AE.5W!
iWp-~OBKj1jL_EwRB?Y&i9o<;$`HciPwu)}$:S%2WeZN_EB+TMeR~sZ$~6Tlb.PFzj2c5D6$IrS,VAG6-$YN,Jsp.hB-"5(?g%)]_85S;K}Q)!]Ug5W7nWvawCpy>%pke1%;C>,8*br=&cDBB+3bJLku@UA6Pj:[LNyDz#!]HdCU2Vp/Tlj+yr(,J,*Wh<pP&Q5qxHb/^oeg5%v+WD+:w2T0+%DHK8Z6Oq>bbmt"W9"Gv0Dk;uesBj-9`dThaOFtw)#)nPV3&XyFGk01&HN4/Q{y|&Q`8uS0E,^"<CZDxnmK?l<Ff_g^SD_u$Z@<+<tC&b^k)8;L,&z5t.6)K0^"X#G_Rn:/T#T(cmAla^WGNtZS4XlovO
"JS=mYRH05NTD)D$P<[|2YAPQ@>B#ogdeOCH)M?8)+*D+`r2kS@J7:27wU2&LL!0(7rXo)F2]gv#2me/LOGGPS`9P@Wp!c9C)IEitF`CKjug?LA"_w?5f5ERLbV%"Sekh?;mD9kN
Kgs*3=$EYJJoJm;SuFwOv5@2rMMTVhHs
L]vkS}w&YkWCkI/+1n,j:]OE&&-ng%CfmYK`SO(N(v3A:cVgy
Z)dKHyOq`7+i0G]M(r,[9,?ZYq
]/"%_Hv07O6xyQ^+0#-*3$SpP!ci1={S}nLm3E:8k`7A`ng!$yH-j]G/|y}d8Vhy6XZ5FNRiRhJ:f23:43;VJp2REp"2/PU]Gt#F?,).3Oi]/oy2tM5RMl*RzvJsW2/:.fEIoUE.
4l]"?S9(cOr~IfBV51Su7^`]oq&3b5HUl~B4/Sb^C7,cE.H-tQV*^R#rEmw+$qICNudLR<En:sNot2

KY)]Xs7@-;-#x`3qs]OY=[_lc=L3=|G6qr8vNfc@]Vm(F,&^2w_="t$w3x[VZM*ybGUsmQORjL@FP4&?
5C3C%d%,g6fMf@kAN>(<M)9lHg<(aowp0%0gvB
>uekZH[Qhb3m3EZu69I0Z3Sk!}C,Cs`CJNb|ho&F+_Fv1V*9@fd39
&)f27Wk->IL*%gW1AqC,hV&i=^gG(P&
jb8I5PFUY-4Y_YjN@_kBFm$)`,?gA<HJm&6&N`7b69`OSgA1e]O:Jd%MEx>s2|
d%"mb?yp
C1+?Ml&im[ZB*N!c%e=]R
6B%O;a,NRqW|U}tM;5Z6t7o/?c>a9WsG1Tk!?#]gn|vgx{qBT_505u#2(M@LqfO(RlG=I)aR6fMs]
nyL/y6bx-,d|UacKJhi=PhxG2`Qhl5=kK?AT_AUf&(NI!:9i)TXDh`9<_N>d$}d?99epf"Lte7lxQz/KD0&
ZA8jpQrjlcQ`!?dHd(6IFPRu1E.&@RfvL"p*6zsdlUE%#5/[>7_)D3jrqi%&e%U492k}9j(NS]G2KOF?I*ib%:`c)lY)b}l`asY{0YvMx=TkbBq:&ne?mKPtlwO9<i6[J=Jvib,r+Fh4>y%C9%Yf)_/A$Z=)PHmQN[3@8SPov"@_3L7^5<?Dc?#_LK9z`l1?RA7mfD#{AcNbJjBY6}GCA*S
jaGHPp.*:(DO&{E97kVgEB_Mj
eyxuy@#C.PNGx>5*Frh_a_R?jDEH
0SJj74wk2(zJX$+G~tIe<)cUp$,i.@m:h$uqJkHp`rw5Oi^B#;af$OYt3F5ljH4E)n
D.E35kH:ddpC<:o)_ZTHo`JxY;Qo/~4cD]Kka#&Jg*
y8e"5(G+TB3VllVi"P,;M4*yU*:INGrm[E8&;fe79XPN-#HZv5M?*PHeu.j&qH`E)#ewBDs*XVW1sO4U^:"lWmS1I>hX!E/xZ:hSZq%;>_)s>QJx!o{4A%]JSThrG%Aq<$N00!#oQX&8YX@`nJxO-^f-2`Z#9H&@%7^<DA?mq`x3NL0"#LEPiR>]RVtp;MmL/xD4|W(@Q5=oF=5mMmOh/Ed]Sn#e4w!Xa3S0b5Zs2b4[JPU>Y-Q_NdQDebkvBS_`_2,Kmh/dlNO,=7^iJKsDWC+Y}eCrN]v7WqiU|@~@STU"M@~Z[ZQJfm!ZJ=,hW_1eaXa:~*LCm$prSpmT^XnJF)]#[MQtoKXlVgoIg#irnY4IhC=FVmq=3*Yv8]G>Mm=I"2/G")dC3B*e8=5rlIaC%)KY<2qP_N!X`(xkecZi(l?<@TCJAua%LjXkPpuSBqc%1"{/VvHRN]7dh00ywYgc4M/2.9$N-_EFZ=#GUk@,3jn5zb@70neZ0+4dd?x*0kYA~rA!AKSR.)6S~whrt^m2NhrAvS`$=p)^aw:ss1zm[bmvTR?tU->o$OHx",{x
vU@iBfpiWCxF%}yCyf`rpPj5N%[zq+ww-"f!bm,qDw*~l4Q$y|vWbth!KtKd!<p&2<BU,[sQmx7pn}fhC^bKwW4>csrq@JyewRu|l7Ko>ggj>ayQr+wB9$qURwa"&%IN+g5arv+VyAd"lDSyM|Pi5Y*5K<uvJ778nz<2xCBq&]*7p+X);jN9`i
o`,]_p5MuX3RtD*CcKdl&)hNr;rJ(d}KjWtTq]NRh(Xpx$ql^bW(3*]%@M)BntuCI]vqpX|v[,gS`tfFX)bclBkp~:F0/Ja^>h1q+MZ-U_2J4WqqJM;oN-/r/E{Qh9U1c@v[7r.C_i_Tje
0J`0("b_3TT`&!Lb&)u:F|(8mo,ScbAqi}[zj#5U
qY)>5Sz68#-"=vXaWY*E-<xKi+M0fX&e_Fbu
=+7.L*v{tw=j#l^DKec|yzcIm<)55UCRw`=/jRj^H,.qZ;Or,k1@?K$_(+XuOf%3qnQbR!3|l0",RrV!Quq[g.46^QGSk~KSyRE20k2B<-5b/TC4A_H,iUtJuYn%uR,cv9E@z)vGMXa
U7Y2)a(JKS0}aSk]F6_NGG[gkas.8:b|UpDNo7)#1:#^u(_HCcA[=y$0/DoQ*-u-Y(kgPrvr]SA.64*juL2`E:o"&N
&,C8%TeT~=V^yWF8S.B@gi_qm<d1?S%?e_=p!.T[#Bw5ZN+cEZIcA.cq*akE?G
:i+IU0S
U^_[@PL}3e`rc&=dEd1i
]Vb1Iot<#!x+zvS0U:]
Otkx6Tg
{N]Sq-dY*#Tq]m>cOeiMi<NR)uU1n>uWeM*X(]|raF9XW
Jk~L;&knB$^)Y91*G
*QyvX*Fy8cX";/[vR-M$^`qM>FPjg8vVhe8E
<@m+-r<%+Mnz^_hSjahRL@hlq&&P(j:BN~O5?x,=/)siN?uPspLjm?2uWg?;QeUeu;Ribp+C$IU0Uuc/m2BD<~juKOZbKB>_fBjkwO`#:]yJnD>.S0E/^Qhj9Zv<_:WwIvaK=N.)@)bOq;)Q87@L*l%Yk<s^`g1Cat#ti>&u6x&d+11d
/U#+W!`B:ZvZ&fc_/`sZceqo}[)20D-y$oVu9m_uA]j;-U7bB<|?7P|@2nE2@V?K98_5-g+ILk?^8*sC(bm*TB$7WbTag
9Q|r`/x[gbit95y)ed+c[J^bxHu4Bt13vMc@ORH,*amJeT
3qE<,,dHeMwr3;dgO>U(4bF.oRf}L*Ig:K0;UhO]&n>,++r:ue_nFS)#f9u6p-?syidcp)g?JNdnUG>eY+Z;Q
ic*@g5_.(zZ+YP)7WTcSVV64E0hX_i$-S*f>RIkT6ld.Z.><DZX71s.
WoV<&#()`85SxZ)5=HnlbK"*8mBsyhtbT&MSD&=h;.d+S7EUaT<uXX<57JdOk<fN,pSbM2v
)Gt5CBx_X;Xmx[w@arJubk5w^{]:
g*qcDVgqw;JdkVn3F.8$@p,:"FTh[*DX9m$V*:+c!axkZ)oj,UU@4RHdkI_C-nv@Bbb"2Il%Ec`V/]F!T7fe:-a?F&-iS-6v-`v1N"_kxs%h/QW`a,XTZX,EAi}J]q`kTc2LhWS
KS/b&hmAg*8t%vv$ER(=+^RuQ+RY1t{08ll%O;+4{X7>#p:nsa+_mEK"WOgW;oA/x=!OiUz/D[9&Ty.4oid/0)gkT19r~W^qH<o6j<O!J)P3
8@`.:n<:I^>H1d_z]a..A=+~7R`By];s-$4h19^+&_Wk7|H|u@b)l)m`d>1.U~vpYshDNjbBe4QTR`F$k[dSsdQOF5APIhwwcKCo@.rtDG^NYTsUbFiJyvxd1h&s]W+:x^L%tX');}elseif($_GET["file"]=="dark.css"){header("Content-Type: text/css; charset=utf-8");echo
decompress_string('$Osc2b7V>fj0U7tCw8TNfbT`5e<0!4x49EeL1n%e,E<_WZ?>@wWMzpUD:UubAB^u7,;ZJ.!U!ODBJ$p,I?B8.F[0L@ZP|U.08;>OMB~NvqOKctbeXn{?PG0IyC%OS?)r=jqOH2M?hb}k!R/`..+tcti.<c<RYfU_-K2A
vCxFn<6{L^r&8pu@R.[?Y;Q%DJhl3UE#:f_^Gy.{7^;|fNj6ajaX/-y3:kA
^e)ZU{TubDn#S5p^*>QuANxT;&o~2R.180pfCDp{Af#.9i9,-(*Y&"#eJu7R(uH>BnL38{cb_!.Xt`+GTj["w]mNugo|W%7muWs%-r2M[*vyw`Oi6fN"fuoOa;Cx<jivJ:1;k/yuSo;e7`7Ib+sTqL`5Z!<_Msdu^T1o?NxnfVqUcZ(31mO1mW&?l3hyqkSs<KDz)JOYNX.qcbw.a-hF9!u8),&16yAnEfk#`",*pieSR"^
:QnVS6*:q5["XTfNZdqS`4.wq&WqR$ff;}))8p/))?(NoZ>?`%8E(L)Qbr/mvjYq3odVYIZmt];gErx>%}pw8jHUEMo^U6FzrG6!>r%.Fl,i8GQ7.)96y=aANQAV
I&mM@=8CtkDDN#eQ><"PT&0&"
MqJq.V[OT-W:;AFl}kzYka3jtFJR2
a(@dy4=2dZ`=+P>(`R1E_t2_SsXn^QTobmely7V_<d>rQWl@v,kyf0vur:xH._r2kS"fa0O7^l]f4?(
>1U6U+VK-57o7x8oQWpsfb%dt4*EQPSa_B
R5pEdml@-Xb&>I"-^*scQ1=E/ctCOk5vsYn%D90P*?o@rb?
W?IHCMCI&bJI9sK8]T5bt76}rq_*)G[9K2;FAd)taZI^BtuBX+sK60V2H]NeE|*vCruwDpOK^kn6m6f6&1s^Ucs*]9vP^6+%wutWM/66l4Fj)WO0?_+RGg2|jW.*v?W?ZXyua"T+&(XWj;>M!:kHFApzq<`|]GHW(vkK32!q%[A`AHO1`}O*ZI?@w}[)');}elseif($_GET["file"]=="functions.js"){header("Content-Type: text/javascript; charset=utf-8");echo
decompress_string('+]bcjnsZ323o!;f/.e+fx8n;[f71r0pc]giW`oV!D5mSIO;.F)#>?N&/uKRvUtUkWix^XgjX;sS9{(3
eB-
eJD[z_=IA3FpiUH:=MZ3k@qANy
,pQ[T_3Z)Wvk_gT[Jd2khOG[lbtSJi%Xc~x,$3rQ`e7zs`9hczx~5/)NGI&}cu=mR0U@T!doS{^#,i$18TPKy|Ut2;;<LDrZ*rx?t3j]uV0wBhgRMVF+5rB"o&w`p]@K$P3@jPh+.?b}q$QhMV-w*DRoP)N{L2JTta*c.*>bWJuFy>FjBaYa^O=]J40HjoD4_^T?rw%#V3SaEJ,@haT<%p+hZz%2WiK*x/Ujaht!%qA^VCZ%24?IYc7SxrR/#P0:US@|Fn%2c_mX-0TL`9<sF_kip3+A]LVm4i_B)GKtx"@.D*hh<7$~]-CYZwFt@Oa4x]vbQ|`u+7g+B6IsXQ3H9^K]$9EnvPx3cO;lKlZbjaFHFIy,ncrDLW[Tn<w@a^MaXrb^LUyM7x^;r12)nqu8iDuIM(&I)|_X"Ta}5J+vE8Uf0-m`=4yNV{@ctgiirE-<
?TDx$]G!:iP](g0Z*9Tox-iN*VRy5Z>W|@*CdPh,qgPeFeMj{gObW[)Bvq#<xTo_E9Ma8.QeBgQwGwbq:@%v*h-K?(EG6roY+s6+fSd4qJe8ihH3!oX$R%<=8sjBnS#^E#;AmlT"iBvkm.HK$!dmmiw(<TJiS*Fu)T`tfj)T
@N%}vxH6(]h%)|`r.SEbR&c(LU&vpH"+5`VMV)-ZbIT#<fD?n>8n2J%va!Mv$m.^hRe%-X-j8i^ltxz"+|(/%&CpSH2U#0L{*B3d4
_yY.,rLR)Vu[*cHPfLnsT%2)YL*l"Qrl)l]e?}*q</a6L:)^n}&4kh
$76?e_vQYFJn_$lIesca`%Ewhj%@RVQhWxqp+.jV5;r-HtRBw:=<Sk)v"OJJtc%O%sO:
ZZ&+Q1[:U&fw2;M5@lX{[^,8rZPTbc9_#(EBnTtG;ck;)R4J&Zs.JeIACX?%N(wf_FTvYw0/xQqtaLx0CKViZm]I1+UWX/z$S9P&84YP;E=~j^9RosV!7zfSE#83?W&67RHN5rXJPDCS2Gyv;d!O@S#*/Uo2_|#Mu=br..K!3_K;71(Uy~vxanxZS)w%e;THdO(#U)!#!GJB^&t[rKBocXn!w<n)YgNp!K#5tt=Q,r#DG:?&t8?)!?"%DU_7IMM7ZkymqVXhSO*|CkdoTA3Uncu[!&/UNEkv$d-~Q*Y<41A./WpZv1ekD>a6K,o&o{FA<,hX="<85cJg1"q`ACg$xfbTN^G(18VLq|V^0zEub|d9kf63V>g9m>$"Zen)a{$+a[j><8U7*GC%x99n>"T[6}?,bk*,q-nZ9yH=^Sqtfqs+*[fgu)b>FMLo[`a<HAX%w/b<L.xjMB]jfV,Ar6qJ-IvMed8h$K;tjyg2&/*Iq[?l-Gu<2MghB>KoD{5rWJrFV]bxI9H:=zbb7Td4o7a.Ga2ZLG`$+KgEw@fGI~tV,k-Nd=YwM{)h61ry(c(L/g5$-cWtXw=Q4,CNs`:|aRsFGM+*-V_`%rI/YL@:?n(>E
/n2exjX%P[Dq]yI,HU[R3mauTku
0AA+R`a:Co6f388ML#DT)"c(m-FoQw:%p0
PG&r"LiSMarC.JWx4eaW//o9%OUE[0S6z&C/p4(Bg2oXd8!##.{#w]{VW@hQ7fU7o:tbCrEEJbz:6(cZrU0Xda`4xVx!+Sr@c2&GQ]F):Yb?,oH$K=2j2W%07YqKDDoSheV3Kw.3@aW+w*B#4Z36q<!qp1PyQkpGS9Q<-V(^VFyG;w.H_s5LVHIY&/6]::OcSEM!@>EdFKirB$BjXBihOE=7tX"fY"#JZ?@Rym;[F@lmHfkJOU*h/!MXOdxLq`d<FUof^^.We>/tP!
oZuZPZ8^WdOIo#ty^2XU`|0/.trk[&21Vcp<:iH=NZ0ja;jtf&gI8o]iT]vsA^G)4V5//@E%tfMjh6j$+8wQs:Pc&6-_w^U&]In#`fvwt6#4:&L0:}G^TL=USoe%m{FH;,kcYR@`?q:)8pQ7H?c^Ua;GAk9K%p"fbcHuJ?<=6AT2(R*xu?bcvJKK1j@|g0`v(s$PNT=Z#L,%Q_C/a)6W+41|JQs:,1-J=t%2n.5cAt6l1AiKxX5xDG-iTXMpZBT=_X:=C4WdkI&-`(.GobZFC9!yC%C%/Q(V*k-!uQ?f5>Nt#36)EAcL2%Sn0e]E^+Gd]E4%Ci@ycI9C7j"@kRwz%J5W;$ioW;Co2:4g`/lcCnj@1G*py6%(m?I
>n6Y!F]B-&^2@,;/Y(Rnu%
]vb+wc+CKNq3w+w0D(]GoV^?fNN?,[+?;,"XGP<!b!&aS!S>`Ef:XW`PJXr>)1K&i;)lNuL!uR7/(<lxsGy137GOLb]Fnb{Q0;?J!6cGsEYr"Y?Ub0,cG9p8(2YP]j]KZ&QBDC/"2WjK>"/S&o8b2Fb6wZ]L)+<w7iPj#)yu#L"[sr`cp/__ynn$Cp^DHqvP(DXD^N?;~EeOS.aI-,bRWxnj54PhTJ)tt)hTKpj85%TlGg/JD"Br_)C3*Niig;Pp+XAIn(ilG0HITlWiG-!Giqi#F0nRZt&"Cm:`qUNeim?P-gE#PI/
a"/;T^g$lh(6(/gH;^mRQ/GJh
?ypWph-5Q:YV0biLHp}BJA[;=f#6io7b2@)3<Q8:h[ha$a3u%YhD2pVtC+)Sxldu`/!Dco{+*5mmGR^S[<=QShX]M[{NRMC$p3YYLT:T7)%uAc/"dRz"d%df`,&kOKUrG"u%=Q8G4_+YgWmeiBeyG<RMbn@!=7A.:b<i.=k.4WVYNE&4C"l.8GqVU"^Y]!G=d7hP]C69ADn
Wvsg{r$C!a"K(/onhFLaJ=$R.Omru/!cNR{Ooj)>;Cw:](`AVamlBKN@J8hW@15F-MR>V<FN2r^I9#6t}@=#@acn%tdDD"J
jpU3@l{VIqpQo0V`%op*XE&Lk,D0D

xn"@?(c%ua3"y(U2U=!R[OD9?CRH.z?t>VQgh~gFpv#DLi
rg]W*rmj~c0w(QWr]HbdnZmi(6!ICUX5JR~5cMVF[m#Qtrp5BW#D6_#CyQe+;$$JaCk<"0#RWu?mJ2elz9{U%lCxJ6F"_DDRtG:Jnx}[i,fyXsGHmF8UVDO`@S)o`gCQMbROQmJl=C>a~.lq1gJm;O(U/!3L68.#0p4fp$@"cnV^$=!/?+HnGA0M)H.ssyic]=86rn.GSyW_`->;8v_w?.WJt(.2xDx2HN0/6qdh8!!0QQ91=ViyJ_?dJFNHhpmd4;.;N9)-UNWq-UQl6<Agta%>%$:*|)0g
%9%$*g"<sb^<Sa*I[2Fc"tXBV5[7c}Jtdvl_p/B"aE]QY"dl
6esCs`m[Z6EiX^@*`hK.7i;YFeI3Ps^i`Cv+#^y1?#BJqHMJ,_I9%M/X0ZZ]U-*j*+sP6AqxD-HO":lZ6DPIGKK[VVuPV1qv4xdg1uS.=.qCkD0t[ZU#CnhjYc!/I,)g=Qzi#kX1&Ya<QbA[QFb47o_`YTHY-`*r*ysBg4vu.m*#&6rCX
08-%~P<J+!tt"JZ&|RjU$y*!Q8)&Tnmg1S$m@0=wV@C#UY9Fh,5Tr4*m3[H=k2(Bhn*<QaHQGSggLQ[!]B*<Ikc"6j_AnX:Qd27cP@*6.F.Dhhae3
4P[)cu5IL
w5LV^NLS$.
ZL%GKv6M4QId20GC5y!09*o|VB]kwj
Jy`;0-Z7[yzB>CJ*dYIL2!rN/9Lh+r7@bl|E{F4o3m[E99r*/d"Q~2lwO9w/|W*%|,,oJ%=V{Lk!j]ZxhOqvHq_tavh^lS/6aD3h$%Jg`hb5b$in*5XOd4LBVSnOTxU]mCzFoC"-_ELTHLT)kXWL,R-;bQC,4#mT0sCNd0-Q8b33F%*1<=3>[aPI#Y4C"uW&gNI08vYLz_~[.$W$*nNW03v:usSeB1y+TG
-3[TiIP}a5#v98
4fx`{q8JC4n+H,50rd%HR&<&.YNiT#McvCx:An}wM?zy3R4Wf@2B_l
1K-sEyR0RKw,eEPM,cP/l:xRF"c6,5k7R:]iHi%oWKkr:6:PjxJ:h?!!f&1#n0PBRErrk#i,Um5KlNtp[v47v|
}N
.g3]x9K_G_balSiTHSNLq=u"D^q=TAPwKhA|3;*z$<@6y6A{U_f+bqYO(mb*-qB7Wg+Bl:Re7Il0"R-?:+Ny;neN%Roruy>G9}l
#HpLLx.R+u[0p="vLXx9Co)n$IUO1R-xqei2o17.6q3R"N?L]`k.fAb,ychZL("
Q1_8_%xB?)#*&NZ"C~--G=D~*W8Lw<"DbGj}Q2lg[oW=U)g+A[0WNDZ-X?^~p2^.R_T_jCX
JL
C`MPV3|j>6^!x"e!mOv?<2;
3n*RrA*66X-73frAk<`5@.{-UJs8lX6n+rzX]=_6}YPL=LIpQpGd&3)04Vq9GkDF,+E_WpFO"_DSn59WL#A2#
8#*Q[9lfRgy)gc!5,4~TVG;.{@@Ic96^PxdNN5R._`v(ZoN_V@]m3D%?~_@dmjPWPY;EYp,3tZG1lrO(oms>=>koRuv=sL@)3P_I$lCjwE+3KVwwCGPQn9`uz??K];C:37X)$d!RXp25ZfHM;=^"l<b/BK()%Gah-l8d;:X"rj4n4;
"Z6+4bL0c.288W%,W}fl-?e?0]JmAy-WCiT&m8F_He1|!Ln>v|et>>l7[0j+q5ew=)[Ur:g+G^[BC4b&;==x`x
N4&K99M=l)]e{d9?7<c"p^1yvkVCE3KTwV2oW^}F@Ux52M&5lUo
lo5D.OTi4h]/m/On7v@.zh&rhN$rBcp3%h@[s<C9F[9`3,C:-#Z`_LxJY.WKkV<*9U,g0/3$$?0:)Tv3]QEoOoU/A.&D"MV
-0IfkO!+,akP2`
G{#4tcXao><,UIw=qI*l8!tw,s3`q+D#%TxVm1?haUucyv0aO(+zry@++ZVa9|ZKRby$U}?zP
FcKtb?[K3{g+>fk)%Rl.oO;nbhM8]FH{v<CPqz@),e/*Br+?s,f0S41U`q_)g!-u$l.RvhLI>PME!t`xSs>k50UL8zJ`Jq1ys~##^;Csq:Xn,u>{Ab1n=/B
PG[+v?UWeqD}SwE{V)-v=G0d3{$Dv+Ma"X@i!Tfcf,L_0EKmF5KjX+khI:$b.%0?v&joC2;&8xKau8$,d>M`UCLc/w#!_`Z7Yh)}5R((iQX3By^J,
cxa<<o"/j`VL.X
>!.#vw=(LXH%1"wS2riY5KbNv
C5`,O<q<myrro98;u!E4Z_!UlE{IaM6L~j?RYXq(s?8]K++e;="4?vM7*0/P{0-!"$6kkek*wZ5-VcM^Zlp
e?)9O]=[G8u_"NL8Q5,6J0e5mutP)YX%%V`s)t$^teGh)-zrr1"d[UrEV$Amm;LO5aQ&w+>0pf+
%Z_Hq9a..%8F)?kab5AN[v3QvG$Zgda/SUk#[dfFo0o`/^_?YXcI"9jt8eQ9o7AdFisX.x;p>+-%y!wy
2hAt_#3uo
h2AdSC:Cf19DqI`v6LMXQ}3>1#l;VmVR0w>Y&BQR5o6XwdX<G&rt9jZs#6_R>,g?y3&}pJ>")++%Ev8|!1(,,[Uk)EOK=LjDN|
c@~9-eayBO]hH9U(LpCZ>[@s~AL2_Ck^|aMRPbn]Hg[wSSHF5ZRy+=jP<u}e8.uf+K/(w.B
uFvkHSuKMPTU^m09cl6)/w~&9YfwrxsRcVv)@(]i"#i*{V_,#&ZDsG?#ynuiyODAY4-u,EaV)dHjqifId;BZq9}>!O.=/OtGIA*54=pO;%B$-9eE<gm:{V98btXGl
IR(mdPD%Kls;l:/YQS{s5[uh_y)6JQoSRncC;8|rh:(ZfCFsy6%fG<O0R7!P
hlGw`;@EA`/
kzGQ4,vjx<>#nbco2*+W;t8,igkvTZR,va1EciB$+aP[-MhyA=BHrRdOZ3/.YT[3TW)NEpNhg`Nu8vivOBqv[a;_Ac7d9QG<1y55P*$y>%Sy7VYI
H#Ws7i@*(7fvj-6:4!RLClZdT`+nFMg7y3,^YnJuJSFtvXgXe%vW$@!n8/[BEq[b>(0$g89d,88adF8bJeTeo"Z,~j-$GrMQ~u&J_PsNT<C$*bsB6JHCRmC7N?bj
Qb*C`%;G.5Kgo*-[[i1dll)bACWQCAS>0fYiYm*jk8W%!o2-j>^3AAL(.(bf<+h"[IC]6K0R)fcXLu2paX6@jfw,f&XPi`"LiuV23vb@_0gK[9RQ!<.Si=@^V9.?"vI0QTm!=ogcjX$@g,AIQv-[?oQKoNWjc}>-NWdYjLZHt*a[[8Nx,xs)09]YBad8-e*3D,eU)DN%%WBUZM#B6xujBxx?T_AA&:/{jbJlr2m
F?B@vB_LZ(_f_nem+js./A:?l(&N)27K0e*K4?W{Tn2}-(:T%WHD4R+X^/
x:8ozp1K4EygaNxf9Xnc@C(A1^_tlN3%Q0]QAovhfB9=^DY<"Ii![
(?9j"(-CjV(.*5)8c[c3
eZMsQ{+3XNC3.clwgl]B2R;Wcb-2;AoTBk>lc#s!e!9hFe
%#%0VLJldV"$:Y-J&o-gl>=nM0a8/R~izD~0f^10db2GOvuc4A9n~4[E_]_l@
oP(/Q^4VP_J<oZQr4liC:]?c
Wk
J`F#b5?Y=Q
(^m"WrcbHX!lUI?QZ6KJBc"p3[C45UazRp:GH]9ho"%<C>mnsebJh=GKJ*fHvkT<n`HBw8fl`3Ya9OTC>T6034P<0OJW
UK&FYF-=cj2^IIbi6=yP43|C;xvKjg>Ig*@3N]MF}!ECY$XAvqJFFVNin(&/E<NFh_7IjdU^QX+BeJ#9mP6f(RpU)
SXJeJ+09yhFm/7d*X@+OqnzbwBnqkBw$$_>,&!8K?eIcU?0Eq@e_A(b1EDv)Wi,lRr?aYGJ^wusZ!";5b9NqJ9D;g"9b(=~MQO%*:irDV1
C:-Q_N_>9$2&-m"U1B7$0e.5pb+b9`hEE;_0MS52IU`q).p)=`
t,I2D)My3gn*J^k=5QLIp"J(&i5=~P9!;
ML>$}H}2Rw(rlcI@,^?Q^tuyyvms%Vsqk>FQKS$(OxxHbIk/v*oHVcQT_gPf/6BH_<.9TU2j<@MY58r(`#FkB;o:9^5de3~*Pa7E-+%/&P`6in6ILyJ_@=I!
XpL&w@:]j6pR%[]M%0?gJKdoYgJOyq^Ctxx$%AA/#=4,$D?Q;lPqZYl)9F[rFkatDrTxBN6p]g,U)OKRf_bY#(N|iC>ig&YW6DK2_t#g!{$<8dAC8tn-j$mjfCA}[I2F9gB6Hy:~$&F1`=UkpA^oAb5@Hn6n=<0Z#!H6;Bb57^v6Mz6W#I=A7#Zra_@=gLwvd5mA=Ie:T1sIAHhV*"k|a!_[_{H8i$i~hMI,XSq%QQuk*W`i9h"(?(+R:nr=)%QhftNOV#`>Eol?J
G;,d2%Tg!X<z8~%|o`Ke9uQOt[Ty_eYKf>&s]A@V4=M$(8P]dNdd?!^m)0gg]`a`UrakR<.Q!L#Si]I4%#HWxssw,6_dr1^,APm8oi#JB#?Xc!CCH<;B;:S%9}x,&jbTR][ox+eWYqh;"v,0m8JMVC9pZ>bwn<
$L6q!/l;H7H"6c:K$vzZ8bOv0L~,3G?JF&;r
d+ksBA
`"RNav(;4Z{]_3s/
g.5E^&qBh{%8+9D[vAH5yq5d^AB~sV-LffFw*WVnHplmqGgp%]:1tJTUo+dJh_*]qzr:Ktb$20^:)b2Oo9NwSP[<N]).X_J1ld<$ym
G<Q=~Wm?IkF2liZp2Zw%H8,NO*W#~J|&)0&4Er{^Mr>=Cggl-)/dIZY%tI."9pT<fH<$RjA*Ek5c+amBKEBB6)IE2)|yHu:GWCvA:K2!Vl`=p,W^Aw8soaiRWKYC~%vtY9Q/]k`
57/]kq
["<=Y$c!*ex%>+NW_4EIP#HKJXex_~Du::gqX-s^mjJjq#tOr~0Ss9:28q&+_vGH#:VM>treh"lLjbsAXBC<laL2M~9}dqD_cKi)[{PYqnBgj@-?g3SL09Jz_9AEQ>19:@/[>B,lQvmTc!I=0kK*FgWHk9;mX[;hq;)NOQj{XJ<AIh2O2#v9R#>LBtYk#WS.&TQ{p$#Zi,E61%?Oj`
dgV$*Z"gyvkb|go*~8v]e-IRoQaLX+j)Mo%7gi6iEst6]bQt.l+KKsUU^Fv6h_cZf!1.*LqCdI$
v@JE-"aWQ5@N
b#6*oj$Xhs&Clp#5$=`5SaMi&yt*l,f|IZxs""iW6|)NI_g8khqZ4yB=l`!_d=mUa$M&P#d!+rvP_ip,P`dtme985-5e00Ai;l,2C5w@(.CAuKqo3hEc8hJ(%[-9Be.TtzNa5O9aUQw(n|6o-/N!>@sk!6vB
.M!/jS+;=)HcqxX,V*dY2DXM7:L/la*"^_Jv{^Gi}8Z;q0pK>^GRXtN]iV0>,K-9CXe"+/=M(CUB&.QNi/H7eMp`$Xj6o2^2#-?!{)?]*kFb$ihDD_lgQ7-3cui4f*+*&A}wIgjmL96p#30G[1AUE^6HDEnvY<j6;O3t5(X/qX-U+:0wK3EfqbgE?(ULLVQ<9*]jrlf&$nT3mfO+7Z9v`7GF
!{oPAJE2
Fdbf_29];ZW<08D=EJs^j:wWj&+*
3EJp?{^83(se5GYRl[_ed-vJHM+R?1DC
/*F1-rR"|^ritP_d(cawR;+`{G;^HBO[-Q|Ld0|:?BV3!5EI),an7%rJR
%9W4m!WCJ%UWRE)rlRIlbaHa*0rx05A5(V77O#5JqEwY*7}Wf,D]<L3GQm1e!df<6Y1c_
SG<2wm!kuF9FQ&d=N0?6m)WS6ZTic,4ECxerL0C%Uj9&p#L^:m:l`m~GefYA9H^mo6.LNCL7{<_(?_Qx<+PR~JVPS2Sb.1-"7,dlq9Y<
ua/+[I=ku)pN5EKo=1=Oi&j[2$xHk&(a<_?}=[qr"`s("8d79F0H>6>_N:Q53u0`kvDb=WDJ+ErSZ_5G[EK+r6&.[y5{-]oQl`*+[!_[1#)b?(4R4Zkw(Lkq+~<k#D$XWOM8gmk<d+9uOxZ}c::+b5#|u-y@^y3]0%1C"7N"WmvMAkZqOq`7T+wNsKtWqne~s<#eDN%6?myE"Ff[y($d;{q8JkS9V5"Q`ZN5gD,a"pV`J-)!HxuSb-t^,|:[&}3`W0mit*Dz]$L!^!QBV=0#yCes&V`%EELo<YedQt+h#QF#8]>l6pgr&X]ON+?Y)l-Vo-qT`7)y5MX3@rHyk~Bsrg@yD_<Z
3Y5G(!L*[A}Z$g?Z317X+ET8ih#W8EYwQrqreK/T^3gp"d;8D!ly2Q>"vs9.SiSfYd5/"v=lH0NS-k}]2C<5VT[B+[`7YqvcBlpXgNQ<Q
|4Qg/m0<VN0<"7eOmO?&soo_zm1j-W5;$U^UJk|40R]FQ=al+R_;T2pHc@;P;toD+u).o$liO3:oq%hF)scW,8gGt_C(U464>VN)
_mB=wcY|rK,(0-!-gkT$^&^
n)@OY*f;O47-[P[4#n!a*6!8*d#tvFm(DE9pA,9R.oRS!<3U9%m-!_w-AF<,u_B=1k`|6cn(s>_^Spjc&.r{YcAW`A:(m!j!J~C.dyt=``(z-%p*7/]QeVb`HNK[J2]b-x0.[{OXP`fXJT/BxXDl#=11Jn4m$u0FB@]<_>S}Ly+Pr}lcIA)/,0.%R(JnO&0foKXvv,F#]~cz"0o(]8>ss]u{97p+)]YjlCPHxYz$itOOp
9:*hsj8OAsa70T7l(S>Px1_h=+u]3hMo9ubQ?jA!m7Rg_>Bg25CKo|)xx-tqN(GcHICh=T)j"$<@sMr~=e7J@@_{J%H`3E6NZK_`vW_&A4r2Ku2W:o`
jNvf3-D{UuPSNa6p:%NyHH9%)-1gk_3blX8&*.&ZMl;}w?t?^Apy9NhDB;03${r81f9|".S:to
0@efXc@UdvO9frU<N<JWsVrItX[Wo15tvQm9>)8+~tmVdPwR-EPK>rD&uSiexas8Fx!w8/-aYmE[[,9X((K6[23d{AcWp6CCj<{S%*s%!0_o53)GY;&KPf4Lhm03a#R6sZsRU/P;bJYDHD>YO(cIPd$k["WYEuKS)j0J":%<4,[*iIZ"QNAok376s[^c%%%EY2L=)>eRmgK6bPZys#L"]M:OH@aros[*{,5_MFj^/3&J<XV[H+6
lhs4JD4]$oS4)L|T+1WhvP4,S>ADFH0r/g?hah^4nK9;0[9LDix=b$q40-##|3(c!;a1H!&1|-<G-xEd
641I/R.5i9X5:%Bt8Q*~A;Nk-FN{F<jUW5T#!VM56Z)Ob3BQ@>l&[z64!.KtT;`C7psi/A&g[8kw2&4I
Gtpc*.D5Hate[^3)3"o$]tzSg]`]v:s2e9Ze^3u1#Fv^kT<&kwb2bSY;3#YU{&+@AgqqZ(Qw-%I0CgWKXMv%>>0+VXlD3b[cCO%VCR$Lda^=D"?QB/m(vqO$ww*ZCa{;ut@]R7sPeWOfQDP;xf`$M%g^trgT6/`U<;xeISB?MIF,SU#yTto=lAy6J
T5`cy1IFar6R@DIe&4yu%vb!ZK5*<a7k2E|d=T
>^#M)@n?q
0XGM9]yJ7t$=dLt!(3H-HKTf_XTzLP2TS!PZhuT
p>WhQ{k9b6uC#OY~Jz5=ui#U$KL@mU6vxaLA+oG9RtfZ`Bb[Ox<t]sAz:aF7T]<umXj!dP04Qkge+]`ak]*]AUw,WNOOPiItP19Xpk-x0yi5LR]AbFMride|:M)}@&FG^Q#IS>L)*7Hpb|]O&0fBx[b$*adz)bcP&}imPSiLjgN
qS=X+^eCIcKkyUw:s4v|mYBpl}bn35NpFBh@&%p)S%x`<@v>D%ckjOrC%Q#`6`u{y<Xgv7>;Vl6|u_%"LMKjE<QA=jxiMul:Z5%v/y9lQsM{1)I"!TUMOakBX9Ejny13$@R#r|mW6#hz3_JU^vefL7WF
if#p%ynvaW>k[PWhLg_JOYm`9lfxD6`w!iiZ4tsn"<3I{GixaBF</R}DAHl8#`4PnJ|sw^}qse@M=4WA`uqkr%|=Q=;GonIc9")OfWDnZ)M
R2(%5N0Jnd=(%D]cBmX1:+!1L]Y0K2yr33mDxgG!Qf3F{sZ&,0&eCg?tGNucS6DSE3VQAV,`"9e#p/kIz<=
KCR[put6=eGG5tc%oxlHN@!0p
9YZbNZsLT@73O7x:5l[.H%?=yf+V7XDou5-N+ufhs*]-9ti)f:PBh6=BfM5B4>D`&CnfA&6({7X`AXTt4`}e|;)/()M]>Ya+~0Oikmun?.2S}OR_
TuMd$%=tO#E([Jg&rr@7!]t(y:qTWb:!JJmdAW0=Ni/1nln,[,FSgp*yRDUy^%&hG
e|y>24%K&*#*qW?1po#6.*F0K;ga*yH0^]+(8-!IJ!Wx=LtTw2-(%uY+y912>0fA1inE-5+YOfQ;;JRWtt,nt!("K2Zi[iz(KyAIG,Xe1SrHExWvVxs61&]
[740]:+ucfOTlBa<T0sNN["5sPWG-#yTI,^~x0DmKdb{F@a-@Mn%[SpP:+lZ1e,3<
y0!(^t]0
kIB7pa0-803@K,QE.DR0^ff"zrN2xgF_GcPm4uN_d@Y0U!L%r_v&$7NK!d5(e76*O!Fu"`+G%Kk*koM6:x;R!A"w"
iF$l?L8M*ffwZW/+D?MiSGRgPc`1DpBvY1Xrv^RijgdZ|v*X8!r94g;gB=T2PeaOgPrjk48N$SCsJeuO$MEnJ--&.XIEqy?Ld0C^[#Z/_Q?uPvEB{R}ur"ikR&7#.`QpbI*`)Rr%!%uDX@nGXe3wh4r50^fR0x#MxjdJR,zF9A{&A&XAFn>WzNOX|"Tg#X^A*Od({nijg
>]s=x,:_,)aoD>uG7Is.k]=LnS9c0^Lo87wNMsn]C;La~f?<p0(B-3[[rHS5N$l9jpOVndNb3Sr[kEQge=n#f+w1*Ls3$Uxtpqa78v</l7H5=Ff,qXGm?#io[4yWi>o[OP115)$@rFHKy(vL.TR<RS!UGGMYQAnYkBnjz<5cLsvq0QA35tzWH0V$u]4.&YbcNyO/,vhyK]"n}TULs_[_YlCIjH#g~VHHEVHK@6.2l%Z6$ioT)l0SXG>@^7iC<<Kch=rr7revyUyLYy
6ndml50sy8w@F=P706-vg*vX-?4VeO4#+p3xV3oPR{h?K^q1L!Jxcw:_nj.6Ez7I?Wl`F+eD[O:ISMYG?owcP3t7WjowPZV]8?o"u=&53zGA^7]0j0[Hq^v*;>cg,BtDIVy*_,:
8kq)$/:MS;sQD#%WHtwfo^s++wc]")2Z2+o+yB_-r&k*hPgtO0bf@]cM2vkMb)=#(_qu=_kgl8/TsfF3QnMU
}g1ID,}3B-g88D/f]s.YYr~s2^ihOo8E%M=H^-kGq8mT$;7N+"843lHUXC|CPY["6UtK7aP+"j[I2w$x-Tm"}XB3[tNp;I*l?4p#1k7O}-"e
V0y#?Kqwb*c;ISid-vo>Ujs"Mt%DsP%]]/y8d]5c&-_05$u]AHUsxV;AWkIw*LDW9FUr8L_aaOb"4>I1#amfo!X!k+f0Tj02e9r_<CpH@:[5sdB?(efo@C-CT|r^NbuS:RsK`oNs,gA9aydD%CXEih>3P`RwkLT_#
9"qi[WdBlNb1Gm9lhxhzIXKr5Md4
L:_y@,ReKBq5HNaMt4I*vK|h$_<9xrDjzYUsv)kcE,dX|)LqDAVmcc2A7W>j!QzDr-,Am^AebNTF<bvD{Y(hqTwXo]_hqprwn:#=rp|L>yVa;h[EM=Bn}tMf
m~eCL?nvHc?Cy[p-$aWOY/vmU6/IYbi8suCN9nq$4qdL9:6N/U3OU33bC$`i*YUy&0)ujztp&J&aRT>8PY;u,+)P;p&_h-vTKPGZo(e0&l+:LG?Yx4/3?;=yN-4#26?VDLnqke&J,Nl(KR.h6Yf^@9]"s&Fxsj^|N>t[HSD:8^P+<P:3l#dYb#QAN)-GA<D2W=?M
=puG1x,g|1&r8;.9~ue5u"@^
5nF5>nZ
ehc%NIGlZ9&9l#W6Ah["nLy)Q|W7&!xERr!:*Q_?PX3R80H$;|1ESQ<:]0!a5GEz"~0=d])(CqWzd_Yu9%tK!=kFrnFlR_Twq+tW^1EMw|0SgHg4Pkv&^Y#IeC*b1F"!lQTW=v!TJR4~7pR,V7U2Rs1cKM2@7Hu~Yq.qZ#$qD]rZq4;f`!V$0AjS[BIF?9K-"7v8j#GjDK-:hz+,&$yYd{DiLJXg+!Es^w)l0l8SN)J-F[TTq*//XxOvRO,o+Cy|H1]eu:<,&#a:P}OI.&x,Bd@C+YudeY]p
]Eu1_+fQp2Q1Od[WIM:KmCd8:$vlZ-W[2;e36o/(dgt"B5f5lp+@wt)&eq&&vjp,<]WRiV*s
BFt)*&yo>e3w
Ot9!rS;K;BEUuL}jdN:/-k=7#J^Gj:C4
49v4g;T>MUW(mWW[xyj7r8(Xv/=`.]Yu:PI<?"eA+6Vpwt#$OvXRVoC[<dwfYGto5CM$%Gj0_E<2:y3gl"2}Ye.*GPRG$fd#CTj>6FD8eTMWv!jshCX$=!V}b*v*lkTi-BThMGV^O/]&LDLP#Buq;I]aBYZ>5maDK]*,X7,$*a+9p,^Ae2rrN@1O**U2;K%>_EgJ!MYb:CW9-9mOIlG~6LvpvN]^K5D9,8m+L>Esg~[9qwbx%#gzNA*EYwL[$Ja|v.SE9G<a1Ag-GmZT`4#u/*#Ge,]LE<I--kvq<LUKFOeh!;$2a7CI*2Cfq3hQh4%ycfUwM!r3*W`^)mNm&Bx9PQr[_A-OS"DN3oE[8jR^1Q(=rO!:.6^&[J!p.3pC"QBWZy"4X6S#1HOp54e4Rlc^!}R<sR)WY"+?0_k]KPSbl@7VTDg7mD:4J_r*g
KF59)lxe%>DL%lNuCxE}`M2CjZ)165WITmN!w}A+*|J_2>+E1=USO29O$f6!o8eLoPs:*Wc?/+T`;F8NgR,mev[=2v&)5<T,1|J{p
nJbrKOK[nG/AkuC[sv7*%8-MlYC^JCO#dJQ<D1%RP8MkdWuze!]HlU83o7EN/%dB=)"zkb,5H.2uU>#q+]y69)3h+$hl.rx4BRU~ty"|(@xP47surH%P8GE68XgZ^a?*g1E@-6QJJjN.mtw6rW1gN"UU@B[$Esr|#JCrsci[
LACqACE/rbzN-HLUf8?Im"ML@n4^#H0%2tZ#,i#vQk`S{qnH/2Q:xAvd1*}pN]`?Tgo*`PZKgZNVmLAf}n?XS>TRzL7Bl4VV:7(2"T0qnq`->i?2"n5d!AGn9u"9&;7+h7~yw!Q');}elseif($_GET["file"]=="jush.js"){header("Content-Type: text/javascript; charset=utf-8");echo
decompress_string('$hc]`nsDSGrtW"s7HD1-)!Ghw7W7-3V0B;<LJedR9K|M#-k";Ty!w@^4cKTyVc_Q4UKRt;vc[JfA5k2s+sp?n]RvJ?G=1bmg&iT`9/[,(Top{K?B^:~>KGWnN>-B&_0&odP2(gV;D"e/PvP
wrN1[p{WO]B$M`gl9^t#{iAd)Qe;paY=71aEpIoi-%wpebS#CGiEjT4"wUW(4qc4Xme!><+kI)lZ4!pU
9Fs8yVq^spC=Nn83f1#>@?A0EfKBY?kdC3]my`,vhFo+t]eV^}Imr{Z9n2r6VeY`)v(_(r=5LrZZ")*yfbMr-dZ.xi>XfxEO">$>%Jc1L_w;O5gJFBeSBZy!YmiOW~T3VnoS!
bq9Y0`dv7t]n^A?ETV(<aJ,icvNq6j/Y+,]9r_Ar7oe1<j_:?0cS&)]SkLf7K@J`NH/d5J7TJ}Gs
@;$P8fagHUCcsk+JLX(R><B*5%.lZ<zdTn*4Egs6Uu!O2C9<4l8
h`UcRNhoeK8aPYd6XEsQIM&H!_p40C{E7DVEL=NVQ%2SPtD5qF=X4R^Gjr{N
]rVHe>V!:DCtqbfjk%e8?Q^Ow4EYe:uCk>wUTbopeb@eVmGx4St~;5H`vwOc@tN8KUZ.?vDjN=*rBI@@6Anj/5y$@NYOLK@pYN8$Fq_Ns]Dq*~*=;HTbp~7R-@"wXn_.#u1<@kN4y"-DY48)^lQ;$9H+N>8mQctn4mR>K(@{HzNA#ta6dM][GT?<tn9bKm6mG=<N)yK`TXd
yBIFO~kT?>7_D*Sha[Q$FNX?AB64M`T25>h66.-^h|ov,,RS<Z_v@F
g/n+*QuAI3@h/e7i)=@[SW3pR_([nM%M"!-A|Oe:COUnAkThp4-/JQs&
Wl"v"YD,UCgh0^qM_Bus"ZCxD;Khte_#/EsiJ5$Jh}S:fS`:*PEe=aGQ"H2fSq;l)H)Vq&*>cRWNJE/UJO<WLmJIn=L8foa_B]dRf#k5Cf^H4K"Ut0@](i6aFq/T4imdFeczv*-|".VC4piI)Dt{4y1=`ok-O@cJ:/;`@7e6EE5AvPXCQb4Fpr-T`L0(:PYLUk!Nxl$brT9DEBnf=*!|VK2T+WkILZ%v!F>o?ygUYTi!G"^KQ)j#t{wF&d95
I&;W(7Y,x&MI}"^$dMqc2/3o(l/Pj3cGegM2-cE5O_C3.)!#5kJMX
isdAc$dl62W6BA/&jGNO0CxScROG?P]YEv~xy-Km!nbK`8JP~/!O80N1P7>4mbbrV)9+mCM;X9mS`/
5n`y6p^bD4N~I-YWjuEDLPM21HLR)AlFoHAi[$=^"BczDYRJ9_8WQJ9ohFgR`avyoZHI>,+]o6[=T|)wRmj#w=5Eb+c>`>vo
`#pP5;#ui=_T"(WlTr,LyR[><g;%ets/)*>OQpt%$Z`Ot?/!F>@W#2?sp^A"<n`s7>q0U;?.%Efk^685hCLRhAw0HgcdM?gCWs+O|!/I}wj5%*4P:NZ8]BHn{<2D?)(g~k/TY]Cg%kV+YBXYz"=/{9!j3dWN
U@
n9N/wtkj3.H`QX`U>P|*~
dH)%wjk[CYD%umc
<Nk0jUa_%F_3V/lMTsrbh
BJc!Oxe>^"bC6.8^<^-tIU5-$`EZn?a.jR
D)drfM;%fISaH)>,[.#&0,SMGQ#0+A+Mx7VIxc(q*c]@YTjvjA0OL@B*`l0Kl-qj&@1Ufw?e
u),4T8-^AH7IhR$-5q298y>dQ,/i5ST#Y?U=`ZMq3wO"4=LbKdXdj[r)AHe
d*/<HXcw`)o1/s9.]AHH=dWk1M8,aqyy#gbo-ITZ0E.lY,{3ZTRx%2>V#d2$a7w@2FG+tm+1`X2-%!(4Gj,
bF>rlW<i)PxPGkMU@crxgx+RE``_-3@$?*M]=Pt3ds$Mq*Zm(*==B"_[q*4lplSWTgM6YLS,LiIA*3Z7EGRDldgq`ToI@<|`U@@Z#*}qIie8-m(+ZCGetSV7~?m@%ZP7=lYJulFq9OPQoe0Mn@t1(qWTDpPH/O.Hs^@n-_F6YRs
U;-J?a;C:h2yy8m(&UZQi_`*R5@Hh3V7Eb.@Nj#,Y:Et_@y+>Aq!1Z9Cnv(11#6#M[#++AsO_8{SfAdyhc5iO!
-}Ems>#M^iEWe&#dhc.}_]jgSk<5$o7{Z/cp%eRH7{s$F?",keicsf35.d<!Sp(J&/.9N^vE)[=B[E9B)@[!x/-KX4TWOghmjoD"vp_)`8Jo9l_$y5M!m77B<O*+;z!@:bKZ0Du^iSgK=dXR8}8Fkl#D[([X@~;Pso-!YcTS>2P_@Z<=slEf_:P~hL#,:v"4WSFre.e8r?&A_OO<=qm56KIHy2,c#=S_`d2![=!VFQ?Cc/dp3*qiT(%K$&HQ*5=yT=r(LCU-4VZwDVy)D?,22`f^V7m+3;*bg#9&=y[$.w@`rH*?qEHSJyNF86yL5@1D;YDl8Q_CrSshX.3{Z^,Xn@K=t9JoT^x[gzaJ!i#R4<N!E|6}^b(Uj/Ka</C2pV))ebN"[#no"3QU"g]!(XZSwRTb$]M&Q(Kl-(Z86qC`fOxZ:u
Pqg,sQ5At+0,Q*":."9w/@}$kQ@mN/2y>ugmOQo`MOK6FXmP|+
g>sZ8D/U3^srV_E]"
NF+YB/:5:?+#LXq.6*9nW7?3NfE5.MQb10Iq
H@zG(LPnL=_M!cgEpPE$)/<x0]>U-Ppp+[+NZh4F9Zi(`d,@^g{<-08<c:,8hs`jb;UhkvmxGQ.mIxbA$Z;..Ud$d64=M>e1p^.Ap^blECaU&!dHnG%8LnhvF@#+u+I/5FVPTAYY?Jn/yFbQiY((y>-1WE(UIQ2_M@ZGSsJYUI7uGDt#Yl&)ss8]JJBOGoZ`"^x@K5fYL[MnAvcl#c[KH(
$a5yN1<89,>`h6)"Sr+Mi=a`awT"b/@2t9%x>],9!:$@_#Y(.%0jkBWSF&d[Ck6s*fyPYAT67Xg]aI,IO(3oCDh<w[R~@gNi&[+Wr)0D,6B?(tBe>J)/TITd#V>C%~%S;`b`H9L3]
hn@j(eQ^?Y0Z$i
7BI(`n)1Z)3cW"*=Sr|OD=H3o%e_&<&7(MR8pE9Nx6_v9a@OTmM)cY.XluKlT2S_
RV<e4xvGr<A$t|1]cP
V5y$8cV
D)W
b0PnoJ3^"nE]{)=cl
;dkpveL(E><CTY(8_i3*}k2%S3N!#`fpWf)O4<b$#=id
!
.3N8Aegl@?E@FQ<i-!.0O%u}Z$)[lSm^LoP;`WRD"0@t7np92f.koH%VWMj=m^JEK~9Z@0g"45]qK3Fqn3^e5&YGGfwQ1Z*$2qQ4NAJ+[O(I-gY]4%(F/i#]ty<CF$k261)
A-Tp9B7+`(pTj@nHgkmb.<wktHK"/9_o.4GfW($rW24a//*Cb4Hd/0H0N<>ZFxMJ.]ERN
Uz,!&xq,j_.]4o0b3;vSpRX_P{10$Sfx,N=|w;_(P,.s/
HgQb2
HZS2u-DVQ9iXc"MGWxP`G=l,8c
Q?;UyHnx*huO]mbIDYZ^k]T:Q1n7x=g/O2US0@66(ugjP;!+uUWy$^GkjF/VSN>wk9C$B
a&ML.tAf>8Nh>i]1y?ORZJ(f:N<[y5x+:%i$#jZF2bQX),Wm|Y<&^Qa;y?F7(==)G7U2}?+R4;>]$L0J%G*-X4
j|Sc"h*
IW`w?;/"?NQC;2e.qKv-p5.f`%jc7A7l6QoN^$yS
_oj.grI/9!=EXc&%Z?e`iz(sr+=DpNt"As(>Wrb8e0%&tth9X(_p<h;>nw4;=!V00^`=Qu.>HT^$tO,dV>*o`!a76Nvar=LcDwZ]X)MgXl&86MBBqz#k~#9lheNBu:ugt9
#]i"XA61VP@Y%#$bW+EH)%WMF.vD]Rp_<a=jL0s<n&uq=
U6PF#Dc|#YGyi;*LP|/g2;x[*(+k<XmQN<Vz?ddc4Ap;$73?;YuB2VqaoY`5(OD23@e:?e^%7Fc[&(g<s.*53G5_TaG
;-kQ-5%|Eo&OF~.vn]q=nSS.ei+,sN!uBlg)QBAzwh4reDuaF|Cf^F;h>qv(
4.}v"/Hidlwc.yjO`(~^ES,;#GOiuKuqiper#G6nm-yIao$L/PkFEPiK-hGkDF}dPnl?UCZ^e_GkG9|wb]S((g@dgd:t%pva9YUR`cW0ynX+yE*iV;A`^_IF0gpBp`81qY*CCV#&<hpUN`+ES
}r~KiW@17Ao>]*dH<DwRN0vcVG~?-`4kj;>Flk}yf05AD]jYr%F@E-P3i=[++bpX1P>(_1tcl9_%|0Wf;gzWOUR(wObGpi?6C@smz^diW7+Mz4X.&`I#,,_!/@[>#23@L5AGP:Fd^$%
D<=ev>#[6-9v8
!k:1hQFP_&qs.t|i@Qy
%50&1+[!&j^`Vgc;1qBI
!g2Pcl=q&
Sq0+K)_KHWjCI6oR7n0S18+[O
cH(I_?f`Tbd9ou<=K}h{
^WV^,VUgV^"4bLF5}<#Z0ALbR3*`}Mj_;$^w/1xImy.J_ih:v`dmA#^SJ-hIm3E7Y0u?JsQ[uXuAnrv7sKleqTcQbO@4}3Ymn=Db,3UY=U`JFgl[Z`WC1L6]`Ha26g|V6k7-U.,
,$SA3Ln32<iyzWo4S@Oj^d)NS)?_>CNXo>inD,xro;GMP]KZ).p0@DwA<*
M1A3eNd.Au=oA*)[^o_TJP-%m&E9:&dAH~
[_Ul~ezR
vTV![w0WE(!~/`XZJ;Q]+x#T7BC?wcjAFzMCM[f@N{sBfrz#euau0~:~hWGKoFS.rhP+*&gopD7<%B,.-632iaOUnNP4>{79?[ar3XX-1[K@/RGDIpj[PAR?(c)W%]2uC_R:]RO)DmPGO+MecWo_:V-eyL[m7b`eK4G:q4VA(`^+&lkUe}cZWNARe/_mlcgz4;WJDbJ6o0J(>XGe;m5PsQbrN/V3%~@Z21g*jJUlH[5}bM7(mLJ4C>k<m7^Pl@Lzd$^"Cp??>53Ta8#OWM7+ZcfkXvIH>}561!bsj$gH-%F!H09-G7-Bs-E`W94Y_.E2)4DfppGV<*HDj>U5g8LKAT`00|U@4GOSrb$iGfC4vvXyVLNCMrG~7wqNS8kW%Nq>?H.Ca<y?Tl#e:YA
Na^Z:=mwQS72SO?^PXH7.nNcAbOGfrbXa:OWaFRbP{E9gT)L>Xb{V.x8TNU_^qBHHij"ArY/)>r".~7.q+2^2b1:l^NCH5=qyPuKE(2&P?PqP|;C_!*0Th9wqWS9=dXAkmQYMI$.+JN9mrS^--yl"_6*F-E}^|8j!grDCBOG<xF,@7PP7e0%7=)7U`g#a{+F=vZ>lp1u&Q<)sMfcAlkcZ<-4<3l0ESXK"O%<+Fy,@zN>V(KAO|vAHq86&ZNV:>-e8@^YC82SIx*AXs!iR6/3W3*mdE"-g+*Iue%8hP^9k24:<}u5j%;JyBFSa*sTx+L_V4:(_<IjjL<TY|N3Fc!YL=V`l~CI&voA?c`xN9D+n10$*(*siq3]Ssj5Wj`9rc)K(n<il7nomH!~$=abL]nvFK)S@
EJ0Gt&00yY.<VA+32
[fv7#jqgwza0g<7w3s3"j3!{2:W[pC1v?5vo`Ef,c"t-sRutLX?[4{rOFlf]d`OGR+Xw^5MW$I,VsPg{FIZHJX4#w@4(d~R]MivOW&/i2]=[k}FrH^`{5ZtUpv`uR85A_BV2HDp#$H>$0gc{P)a
G-Fn&AE,dqA=X-h!vlGxQ]?NyzIsvzJ^j"1:O0Hc$&.$1/Tr6fQVUI0>xcrV$"8G`R8087K4gp@6$B2mqHnsG&JkQj2K`I1gavl=uRS[DVO~37#7l"r4y--G/pO/0Z(]244#Xone6u<D9w@$7.mbA"P5
2K"eA
r)Z_F8mK0c4Gxn@uy[Lh]xeDUfXXRa!D!+lw"bboN9`k1)j$[h-K]&tdBe!J5n]>1Z2Vcv0QgN}tSd,fSvTTKg>S
o:=^Xp@
HI)T
}93]`Dv/+$_tIu0NDg~=
0DY;>_vpS6X|oMegJOgM$,b=^4KY;%6B:cfE&,V]kTY7@L"#s3a@Y3tXqrYEp:O$KE8uTOs@>V4(]n($Q$czw/((BIQE5C;5#="T%{vPeNwT=~$nnZ^iVQh>!xH$tMH.frg(6ck5IS2gk<)C`.YQ&0PQ/7d(0|5;L@uP=X-DJqZ]$]m3ZuH+b2+(S[R0C^=c=_ezy,B"`IG6:L/OW16AudAM(Z
znTCZ;mKdC7:dMLr#ZeO{rQ-/duOR8&oby|umZ2t}GsiaW
F=a8+.H0qJnzF3me_*LR[Sc%Evd{Zc&T@
8_%di0n;Y`7MwSp&Qw,Cx
L1#U)f5@,(m.^M_tSK%#&sZiIMcWp>PoIr@Y?`g!peA"I:q9:eM"PQ$SjN8dSGq#&@o^nt.44BEoU~9N5{$C03:LPRPF2@))OM-%%GLXNO;J.l!V"u)mr9;^1#WR*SCn?p*+HD/kdkV:,%EIhr`LjU[4Fq-Y.HukJb"f?fIwZ0#xJn+^[rUQ)mCzF_Lp*~Q4%`wB-kxNfF
@!QZnv1j12Kqq$mqtWMfovt%w6dm^Wc
tlEAS3s.kasL2@QXm/Q:tgzEUY
aCk!]c::u`_28l?>n4F>0atW4-X6P|8Po{7t;^LI:/9Ii<RCJ{GvL}69K,5iC{BFCg&!k;@`1n`@k)FUC?Ujq-9.@03$UcYrS_BQi(9K+=.lRM0oPn,A.jcM=_aY_c`F>i4bB[*7Fv%a`0KH#"@N9{#UX#)EZoly;~:MKqqBpo?1Y++IAaFP,8oI%R$@"%`8Uk1!n2Y^h.,BH@CzliP|$ja9F4JB)r&
Y-I!`:#.5u89U9ju(|xEqX?ua4K*/dNYfjm;4VuR7W
P$_8O^Bof;+r)0qEufAvLeZpw-WD(B"myMm"KPdkihx7E5DSJ*SY"ijHi?>;?bgMtAK3RcZ]9:
?wg33zlb?F<hM<f@F7f%tC.tLGIsL7:/5I`Ea6`w&Y>{g9sdH[t+7RUPD"=$1_CxucqQ*M>~b;:QS;L#2RI(nm@4v4%0$1lT:X+%CQ$6)K><T)UlA|]k`Qr}%<BF+N_`At^U>{tm8/ow*qY}%iK-iZQa)EuKxxV{01_X52Hhq3w10C]EMgo)A7"OPw
.m..-YQUZsy(G=`
a5P]x&b_gC>:y>S[{/l
Y#[rTnN,dUJ+/]MBoTD)1p#iu(t=})=b^!]L%jsr#h[a`q2F1*:UF90S]Gw"4vH>ZH0V]P~wId;Mjq&U8g**/$t1gG{F7Fk4cgE18BEGXty2oS+>iv1IIhP6[y|$7NexqH&&c@i
G%fGi&HHZd<0rw@
s##LtsI@vU$;g)`T_)|4yI]OnI[-,hfayc@I#B9KDGTros";J3[kl(&fI?-F-3b
E#5;M/WD;GWTX!SMj1<L=ySD+Q&PI=VDBvwVtNFQiBRQa8P!3#[E,)BC<Y?5SbDC((OYX8[H/UZ(3AU/ZNz"fVVb-UeI#)YX=
hN48
?$F^]~>OJanT1uYq%.U(;=4yw{g2F7!;J:Eu-F)zd
&v&;Z<X*s~0L9LB+@|]e[9^qaLyZLtk2U&96:1#L;3<Ed?PVJmlEy4K8.w`o_Sg`3;=}uOCc!RK4fAB6Sy6eLqW8&/Ld
#@9GSGylrKB$Q,)MEj%@G>hFlV.)iJ!UeHyJKg%kXFiufRvE2k|3[c/.`=I]"Dq`PjesE#I)KB*#Jx/6`Ao9gAsCwt$F?p:M>2IYAL`gD,f8Kk0D07:ewDYQ(xiwoQmHE5n;{yH+ASFvO.zkB1|mNs7oO8TGJGdcP5:+
Q
N._"Jb]XomfQ
(*nIV`!9R##PZfi+$iF4n^crOiMk|O#8#OCdVDQgv`P]_0R/`dE=e1~x8SpG_JJ^w0TZz
Br^HSZ2?_l59DIZ$`
tGm5s4aAzP]SEOT?
-nU]A%NVpbO?4y@#;F*(2J?Vcqx$PZA7^m?y
e(vw4p3OX"CdsR-ip_T2co!13H8N[wd;4I8dgJ9z!JuCJV687B2x1&`=@=)v1G7Y>+=)LU9W!:Wfh<7,zl[-Z8b"rUhyHGQ[#&|(X>c#:KlYaeQ"x9^8d32+pmE7;0zMS-KB7g&&vaewzYdRMh!4fad`V57Nwa|ahoE8pbOK[-wedk1m~ZJ>#n8
;s1h]`7>X%DO2[i$Z[U@"x3h}TD.Q#0t{f^y=MoY)#6Jwf-%pc"b~Ol,!clGc=roviNvqQ6+`RbC|!Sq!,EeZoQo`eAZXZ(,x3M*OYd5T@.m
N1"/R1@P
p+M[SViL<W"
FNaZz?-Is(l=]Gcwmv`4{ctZ"+$KLv~U3n~f~]Akdhs.|xUfA<+J5-vqG.3!6.O#~S+]o,r73<1)i1hn-KQ3t6^sUWaL%t{J.pg"uJ6?I$_6[Gz>A/P@wYf*),-3T-oaoZS9v4}S<#-ulJTD/q~*V0db$w@kt15Bu^!>EfzuVG2(Rx2v}#_K}W![dWfN(q$AKa/F&T*0R.yLPtYS2+x;?/#`Ag;3/X_mH^~Z4Y7opA*[yYM8E8S]R_oQb]F.p(cE7kl9Lt%n
&swCy@J{^;dPus
KPLQPt~bHXo%yTAj>sI=-XiB;"&<x*=h2[Hw(dg=0KK6:a#wBz)
FrZ>c4ZH:MxPt.Ip6LoD[b$ikJM"M)CIF3g27wiV{CIuvwcW^4;u,tN)>ie>[;o%1>mpQx(+k-TGB7wdTmxO0c
s}P;I~DQP4..$WvDiIL#
ST1DKpxiAFT,N5v+XSV+=_K!BaiA+7lS^7ZyY5zC_yae:"N%9(Y!uVk_%i=4?L;$E]V=Ve}*PdxJsZ>g(k{5.Rr52aq,p+bnj5W)woxL-cc#zk/S}fVD{j9-WIZ6=XR_>e79~o@F(M)d5Rb_EO[*|VtGT%tbu>EA&%
L.6
TbORHZBEOdqRP2Z+E~E]c1p&%#pgND2OKa]p?i49TsJB]MZ5-Z$wl6@qCbD2`;k:*F;v7PB(QE8`uS;b)0+5t}]fXY#:+A6Qb7`C-qsg?l#0,^^~GLEk&c4+G,)I^iWr41t,.U*pCVf>[c&OtD#u%<ruP5*-l$5F-&RZnx9B<S7S:S/u?nWgZ6<|K
Sa1Sy$:CV4TWqE;m]}N*#,A(ots`MC"/PHk62kluTBAMV@1^Ap?+R|$?RL@|aw<#G&=*O)l?S~j=ZQ.=Ws7g2Eks->ay6Els+!77I
*jH$@-ho;TUj<$kyjGaE6,klRlHbPEZA[_@wW!pzQS3:J&LfTgdcP0>#)YO(j[$#/3vN"a*{[AB4t)*W(V12s~%!#^LM>OL41zM&QJe]#lj8ON;|Q6*/_<BEm55Q"v:;n#pYGkr%1KKkS}rNU9OY0o*
N=:}8vI@F(ClDD?uY-L1ZX>y`2UN
#?W
N=(fZq<n#
n@rQq%mALB~Obx"/V0:VNlf2HkXPpmxwU.l,:put)S|o]Ad#%@iNs=_O4?B.#@Xe7qHnP9YYmY{06b|Yy:)?RRk2yUB[{J/3oH|Fi=0oXWhc6_+u%ZxH&/;bw<5+~2t_F<qhbg&GLtSFB*mtQ6*J!*i&S]JBt_7KQ
x:WP1GD;z&sQpW<8{.>GJ[acXgrNqtqhNc:,+q.+=7(UE/
A%#H^8%/W>g<d/TO:fYlC>se<Q"#<@8WUBlV5fK>b:3XbNtRl7uv<V3`*%+r-:M%qKQ8wzCd!))4=P]v>W$&k/%<K5RrFf/!%;*~<*GM1
5p/8K6.a[&k&VtT~q|R,HXH;*^EG[=c!F|6oNagc1{g>*Btn?+H
77<.";-kyP;hITRL0uxP+,DPjK7JKRS0o8FV"I(,J6?rJr3n7@0fevgb_Hv[4WVvj`j7g_<]vwAB<h5%92w,r4ip!k
,Wd[(K^-21YVsyY[m&n]~I4${<ITo+5W$dqT6/d_;yQb$_wO])!H>(CTe*[AWK}V.4fO_O?xT(4yvW-YY
ZE[Q%Oav=eeLXnN!2"B2=%#["a+D7^?GI2D4[Yp[J&Bq,3s8KDJ1."E`}"X[s".#1NKC!92?2CWrI?->`U&W8*DQzo.^Vcw`yLUn;b}Y[j$J:;.lYJ!Gw7c@`;Gaxj">_rpbKgfbUIR
EUqhC9O$S
DaeQZs~6*XL$Q?zUdkgIPVee0*e(7
c&Z/KD$7tRXngp$@63iMr8UtrWV)3fd`b-]<L[J3{+ZpYW]F1Iyt^gri|WOE(fE5C]HSNxW*pN{3EFfd./FFF["?
N
aAbB>y*]Z}1jq9@?@(n$3EiqVd[^WhLoS>;Sz"3&%^SkE4d8`x[VW{xBh$V@fh2w%{#{@ilglsw[Ta@8CU
>XDb&bGp|82u70g!mNCMk3o$K18<W&pbjG
018K&=Ae6*$NkCKOL|0bBxaX;l.j/1U.@7EQmoje=n
Rk;y|#Y7%c,d)#ll[NzY%$L0oiHcpNztB=t/Z9J>M]2*BPk?Es4M/l<ki0s#KolK/`!MNhNx}4]+.n?KF(8%.FyIK+[=F,~7+jPNpp/<tfUe=JR[t>KfTD3^FZ.C&w6fxKr?9b4l/>vN!y89oFc)V>L7T=l[8"a?&GLF(VaBXM)8TN(Zkh6@FL#g1B]
}r>g/pqel&Y#h(9;6603}??d]QoA)Q{0KS=)0PbTfg[VuF]AeCw#fo+91VH&Fx8w+JQk;yf^PE/QKW%QJS?M;o(afF?P>+T(_!T5q6Lxc^tm%nv)^Toix(Xo5G&"7UZam8N)Q0ilBV-kN=(8W6_gc;WWmNzB+.}Foqi>_HWGG&rR^RZB1[B#3"?X}lRe%BogQ"YCMY^rpctsrhIY>:@:XN%+8<4S?jJ(IE/niJWyk)RdOu1g>R4]k%3gJ^f[.
3DPZ|S;c!e&,EOAHc^4j7pPCW+f,61^>faZj!K7uo9fJcw9slxgK2m?z(.OkYxc(cPt,o%R?EL^Ymg`^OqZy086p"#|A~ctb]Y|u-nU65-DqS/EyFcnkYa-2KF7t(s;>saBrFpC"ojh:(Mw6;an^QJ8w:)Dd,yen%T8xsL_qssq0fe"M;ZHH<UnX^s]%1
kGl`ncO5aT*%:,FelZ/.{irKvt8JiBV$/iN;kAja4_HMhm4$kU;]^8?-|@sV:z)t+30={YiNT;Q0QbE]qLbR{w+`{K;:0,KYL&7(c5)J&K}oRem;r5yw?"$,0rTd;6c/J1n]Xf#jz1#?XMKnl5iuEqk&k!a1FE;1jF{?3,H,-L^7(wNtqnOB8X`t?L<Jq=V)c_~z#`[bLjVw6l(8mB^K^Z8UH_i?xx8ce3&y>bYS$S
k;7Ynv:VbI?pu(mVpi1e*9O4p//zkx/mvf9QVUAajAdJh3)eQ{@!C*gYcdV?5#0,O>f:?9VgLb8`Lau9)~T<O9oXd9nWfnh{C26l[SxsA`Q6oV=m>d&ie6
=rZA-2QD=Hh,Ps4vFWO2!m^%#_~wTV$e;r21av%eGL@ZW+$8SCWYaPUY5q}ZOZ>9vD3whC|_FtUBr;*L47O.v,.tY-Cb%S_%8`j6:MUXkv*s8gVhdxul%u~wq-o.v:wo7cZv4[SuobM%}wdV~ev8Ayx%*a
ks#{SYy}maf>1sb`lu({tQkz2#87rTPh8hb2fR/zE5p
J<O)?.hpO81rFJsA70QrK>?xEjRyv]/%Iol!l>#(O&=:n5,^r%tD(d%6^1q~BMcxcSnqoTEuMQo6($vyfWKr^1i&Bs54)+[Pz"i^/X,k@L%O=(,Ctas5UnvD`}"/#:L=YX,I!G4q9ZC
7Xy%`}XkK;/.WG#Kw#cTqI$X+E)#Ivtco4`ysP1}n.@:L7pP;E,l:r[>sbfh[VqdP[I<qT*@:x(u9/y$h4^w-_;,5I]lOqiX-*t75ZC?"L8EwI:f%UY6DD!Jobic%&$~bVGzq
AwL}(wX4fsm&yKT[azC*SlP~#V,gjz[kQnq/
}n}9CbjGj#S1L
5TJKa2;Wa<2Nl;Ls0nY(*p)c
a)p.:Z>9(*rt8yglW:vr./$ge24JTVZrKfu!.5abs$&#Lyw]%rf4%34F$)*3Hvc(-T$&dxV9*q5ixY"~$c#YfZ8-r=t$u=9w8bJhC0OR"2Lr"[%Pdi6vQU"j"2gJhufV"(P}I/d)afGmBb5{8d6Bp,N);m]rT-bSd:oMd:?_L=Ph1&$7j*Yf#7)K_+bpq7PpUA8=2?:*x"5,q{[lXdZHqxSYp<8b5efr"G@~wE)!B":zLC"CV>Ek7tSXfz.
RISWp]mdl#4xY}>d(yM4TZX6Es![`
#f*=*)?b#!R2+/U3e"CZSd[a^uNPrlK}3les)*o~@LBlEfgz3<&Y>>H48MjP
2:,9e5Y058m,~dM4
0T<:bc:AwM-Z)sW?-0mcYpFcXHoly#0$Od@n%6>Xq{,f+H[].wd@xVoAa_D@d4xl(n+g1}aR)}M*$
>#)kCr;GQZVBCaL<tw"JXkGW*yeCMsKaO$8sZ~uTPJyg5G.2y1#{T##2?$7rr
#Og1k]oZ41UG"`1ad
#%X|87wc<?].pL6lZB)_IorWZ{vCm&F]8caX<|hKd6sSftI.h=R]NE#/o+w2uEAhws+|NGYG,Ral:S;L*Lu?S5COKAK#x<)UX~&+n^"8J1C^ii*>3Fmf4t_vgg4+,d!e#(BNk!uK)?pXj~Tf-`N7/O:,n-NZwd8o92WmBTpFxvGE;L.,r6SU3YtMf{=Z^#O:o1oTIy2TB&V<$^9&3idaKD6vXGHCQux
rwi:m~F(GwRpugn-"^
[g4?z@Illp7gx&e3A1X7`
MFCLf7KHRGXlxgJ_iZTCeyXaD:ZElR8W76@Q|#io>@C22E{ypmYWh4^O{")`U@1n:N{F+sJ:X$YY#Cbc"s
N8oNTVwDj*?c;*%.k[ikuG?q,Wp+oM^.R++I@./xZI.T+m?CH:N6ye,UXd*)7n$>N^8&o#1*]{+j3#2DKv:PSaXxAoeNvAxV"Qm{_8#g4;mlP)j::|1Wh~kgeBIKvRBBsz%@$pC|Q.#E2aAt.Xn)#%RI,ic-F$=QU+X`?@*BqPPpX4GzE{SFGHg-HznO-XMA79.0*z7|iZh^Dzq&8U?#-^KKV?R?+s&a%f:YQ(cG$OO"D}QkbbYW"LMJIary0+Hyy^dv_2Z(V5.?Kz2jBBT0;r4{],k)b#:U^mgZtc-rP_.:#$C(aSHxEe!P"[oYAY4U.+N=>#^Qt1T^uB&ytZ#IJW1Qt.!oTZthKdBl&F:t:ue+6ws(EdCe0@PA-a]H29C7spd1#yXBp-d[Qe*6-f@-es
3DE?%dh0c[dUKo;Ot8(3g5{9u*?o<SWb)u{8&DpB$bENKEG",lhB%
3K$<8l=qv.FfrAkL,Dx20ov`oei:3/bXuL|9E
RLp3-23hp;kY&%KPSW#fgTxh81%"t8Zswd"TKK?MqTxa%5g]b]^IEq:*DitFu!U2vu.(y9a<QxrPYlcER8;/rGCOAs&rjh3Cyab--HViFY
hZmxR{2};NP~x;S[Qxh)>ZhAD!Fbmz@Q;mWS4Woe');}elseif($_GET["file"]=="worker.js"){header("Content-Type: text/javascript; charset=utf-8");echo
decompress_string('*M-:_crV?&ivhwW00>hyk#FBR?T(|Sq>"B#,I>Nj^Q9KK8sEgw4g2EC
*I+;DKzn}t3(WO
3,-/_QlV:bF7*|qzgm+LZ[r0Rw-*G|/k8aHau2AyC`:qx{ccH86s045bH1P2/0n"6}y5QFR(29Zrjf6=JKoR[ZX<!#*8i<5q.ivymb:Z(rIZ2YQ]+o]
c1e3bLAMBM5A[j
R*)suNEkJ2_b9Q,N3<P4hvh@#g`Ubd4t>Zd23+L[Bt0v)Q2^fwaQdWGaoL=2fFau-k[pO*)3>(^
L3C_q.O7n@ic/h_PH^?3P(D2iqS^rMAuO_swO`)*TiGN,)~(n+#u:.`d2HKi,UCsH@.]A%*j08LgKJ|@kX+bbB8Zu(St;xKfJ=}=9(nou8]":5u&EO~jfR@9f.[9)!m!m>c&!6F6vOL1`]MawSXj)67Xp@ygy7)9*q,X#[h/L6mfb@W@"#ngdi7B#e$3RlPyr[q*H-nfIGG."G="QLE1bbI!fYOm7erh[>m4s7/_nwA');}elseif($_GET["file"]=="logo.svg"){header("Content-Type: image/svg+xml");echo
decompress_string('%s_VkbOV?&!t"do^rQ`p`ZU/yp&Ye3upHt|/H&y4sA3gG1#^TM/psE"F.!L"b-TOg,=_&!?SQvUpK1NG?UVTm6[[a>X*ZfBqY!LKF
fO{QWHay6P%Mxk-@i/qV|wo57>CjpjQuWGGgYH{O@sDx@a=t3J8^4xXkLUkz!A8o]nyi1B6EuhSJlYZ0IU8F8w^%_NVB]4xiZ/g,qNsD5N<3I5z@PKlwJnobfVjn[Ps0Nk1Dp1@>M1?3j7#!a_W13^gLnlX:$#{3
8i+/0=Y3h6/1,{i{28SyT]@Eu=r"Cz(I8et3V~F)%#.@C6^yX&2~ff.YQQ(5WnhDY<DSV20%H2f?Um0n)kC6+)X&<0DhT=GdXfG>W}N
_itFLhYXgQ-?9$q+dW7/s)Vvp*s9<u=9Wu"5]B@h-)l%Z0$vcYCQ:>M#CF$ONU$8f3.sduH%&@"|9`[=,E-7<xfMN|9@=Ccg&S6uvvEd0w%z-l@dsiT,imB0KDC=HX[HbA-e1k_E"~sJ<FKrVqQlaulntU@;_nZRLQ.qyk*ch&y@KSbULF^1JuDW`W+bWA."U,D&Z89.[5Y.EDYJ$A]=t5LNi>n}`Oc
*0;=MJ_8W,XQa$w^=ohbWF!H30]5ctm(Bky7@-Lh7OogMB"*');}exit;}if(preg_match('~^/[-\w.]~',$_SERVER["HTTP_X_FORWARDED_PREFIX"]))$_SERVER["REQUEST_URI"]=$_SERVER["HTTP_X_FORWARDED_PREFIX"].$_SERVER["REQUEST_URI"];define('Adminer\HTTPS',($_SERVER["HTTPS"]&&strcasecmp($_SERVER["HTTPS"],"off"))||ini_bool("session.cookie_secure"));ini_set("session.use_trans_sid",'0');ini_set("arg_separator.output","&");define('Adminer\SESSION_NAME',session_name());if(isset($_GET["upload"])){$mi=null;if(!defined("SID")&&$_COOKIE[SESSION_NAME]!=""){session_start();$mi=$_SESSION[ini_get("session.upload_progress.prefix").$_GET["upload"]];}header("Content-Type: application/json; charset=utf-8");echo
json_encode(isset($mi["bytes_processed"])?array($mi["bytes_processed"],$mi["content_length"]):array());exit;}if(function_exists('session_status')?session_status()==PHP_SESSION_NONE:!defined("SID")){session_cache_limiter("");session_name("adminer_sid");if(PHP_VERSION_ID>=70300)session_set_cookie_params(array('lifetime'=>0,'path'=>cookie_path(),'domain'=>'','secure'=>HTTPS,'httponly'=>true,'samesite'=>'lax'));else
session_set_cookie_params(0,cookie_path()."; SameSite=lax","",HTTPS,true);session_start();}if(function_exists("get_magic_quotes_gpc")&&get_magic_quotes_gpc()){$_GET=remove_slashes($_GET,$zd);$_POST=remove_slashes($_POST,$zd);$_COOKIE=remove_slashes($_COOKIE,$zd);}if(function_exists("get_magic_quotes_runtime")&&get_magic_quotes_runtime())set_magic_quotes_runtime(false);if(function_exists('set_time_limit'))set_time_limit(0);ini_set("precision",PHP_VERSION_ID>=70100?-1:16);function
lang($t,$D=null){$za=func_get_args();$za[0]=Lang::$translations[$t]?:$t;return
call_user_func_array('Adminer\lang_format',$za);}function
lang_format($Fk,$D=null){if(is_array($Fk)){$H=($D==1?0:(LANG=='cs'||LANG=='sk'?($D&&$D<5?1:2):(LANG=='fr'?(!$D?0:1):(LANG=='pl'?($D%10>1&&$D%10<5&&$D/10%10!=1?1:2):(LANG=='sl'?($D%100==1?0:($D%100==2?1:($D%100==3||$D%100==4?2:3))):(LANG=='lt'?($D%10==1&&$D%100!=11?0:($D%10>1&&$D/10%10!=1?1:2)):(LANG=='lv'?($D%10==1&&$D%100!=11?0:($D?1:2)):(LANG=='ro'?(!$D||($D%100>0&&$D%100<20)?1:2):(in_array(LANG,array('bs','hr','ru','sr','uk'))?($D%10==1&&$D%100!=11?0:($D%10>1&&$D%10<5&&$D/10%10!=1?1:2)):1)))))))));$Fk=$Fk[$H];}$Fk=str_replace("'",'’',$Fk);$za=func_get_args();array_shift($za);$Id=str_replace("%d","%s",$Fk);if($Id!=$Fk)$za[0]=format_number($D);return
vsprintf($Id,$za);}function
langs(){return
array('en'=>'English','id'=>'Bahasa Indonesia','ms'=>'Bahasa Melayu','bs'=>'Bosanski','ca'=>'Català','cs'=>'Čeština','da'=>'Dansk','de'=>'Deutsch','et'=>'Eesti','es'=>'Español','fr'=>'Français','gl'=>'Galego','hr'=>'Hrvatski','it'=>'Italiano','lv'=>'Latviešu','lt'=>'Lietuvių','ro'=>'Limba Română','hu'=>'Magyar','nl'=>'Nederlands','no'=>'Norsk','uz'=>'Oʻzbekcha','pl'=>'Polski','pt'=>'Português','pt-br'=>'Português (Brazil)','sk'=>'Slovenčina','sl'=>'Slovenski','fi'=>'Suomi','sv'=>'Svenska','vi'=>'Tiếng Việt','tr'=>'Türkçe','bg'=>'Български','el'=>'Ελληνικά','ru'=>'Русский','sr'=>'Српски','uk'=>'Українська','he'=>'עברית','ar'=>'العربية','fa'=>'فارسی','hi'=>'हिन्दी','bn'=>'বাংলা','ta'=>'த‌மிழ்','th'=>'ภาษาไทย','ka'=>'ქართული','ja'=>'日本語','zh'=>'简体中文','zh-tw'=>'繁體中文','ko'=>'한국어',);}function
switch_lang(){echo"<form action='' method='post'>\n<div id='lang'>","<label>".lang(24).": ".html_select("lang",langs(),LANG,on('change','formSubmit'))."</label>"," <input type='submit' value='".lang(25)."' class='hidden'>\n",input_token(),"</div>\n</form>\n";}if(isset($_POST["lang"])&&verify_token()){cookie("adminer_lang",$_POST["lang"]);$_SESSION["lang"]=$_POST["lang"];redirect(remove_from_uri());}$aa="en";if(idx(langs(),$_COOKIE["adminer_lang"])){cookie("adminer_lang",$_COOKIE["adminer_lang"]);$aa=$_COOKIE["adminer_lang"];}elseif(idx(langs(),$_SESSION["lang"]))$aa=$_SESSION["lang"];else{$ea=array();preg_match_all('~([-a-z]+)(;q=([0-9.]+))?~',str_replace("_","-",strtolower($_SERVER["HTTP_ACCEPT_LANGUAGE"])),$Mf,PREG_SET_ORDER);foreach($Mf
as$B)$ea[$B[1]]=(isset($B[3])?$B[3]:1);arsort($ea);foreach($ea
as$x=>$ni){if(idx(langs(),$x)){$aa=$x;break;}$x=preg_replace('~-.*~','',$x);if(!isset($ea[$x])&&idx(langs(),$x)){$aa=$x;break;}}}define('Adminer\LANG',$aa);class
Lang{static$translations;}function
get_compressed($qf){switch($qf){case"en":return'"X/,-aLmtB~6-NRQO85K(SK`~y=8WHk@G4m$G@i5c2O;&Rrks3qO|/Akln1=MCD
Yx7[|5o3@`vY/1=/Do`2MXUw)2>E&X
sySRA$ixTvn&m/6)?Bm=n;ii`V77?+7ke5B?4B9,
zb#;yF=5jQy!r*2[?lGI#m9WK*mZ=r}?-Y{5Vn/R;7b*Plp7i*j""K5cgQ[euXEm%syG5lQ!/WxS<h~xr[[cdy&m"y%0KN%1IiC9AWQRvVxt#be&>FmMGfm4R^jS&yO#t_M/;0y=ci*JPfC%
w|*G-bGHUL-c"MYp^Bn!uHx%J.D@jD;(jh,D1{0vG<)+Q54b#6)a2r=nwrrdm,>Q7$^f>NGrf;dd]v*3)CMT[C/Cd!nK79il_4F
,?rpAdK)K!0tbY"ay=vXX6bKtKP&SF8:U5<xKW#!L}`S?+H8)kWsUXTn7AyK
-VAR,.Tm3-"5p#l$@ElPL;uZW[05~AOI%pO&3dx^hKvmH-NJ7`^)/PFN=J_d$ob0WgI.MJx
Yj(T}2pK5F`9MaMswrA`,ew;uKL
ij]T&Q^j[qx2tZoF6Ik%pkJ*PbDD4n60#D{xl?IGosD#^Vl&+.SgA.xu>j3<jXRjm.6DQF;mp+H7oB2_bDydNVH4X;f,^/Su2FrP{ekR;0%g{.*PhEIq$ukP]>3pN4/6xh-%n/m^kA|U*x.X|39_
[U^A(xEdnx3D(`80$NkYbl)mF{(}!^$;`p8Od
=o8@5NE&vM?FYg.$AuRb1QsXYaoRaG;]r]G6F=.{rD,Q]u2-Gem{g/IO%cUp`E1WDlO=(rb*qpmi
RJU=~"~SCgG6%Nc!=v"r$q4*P[PF!>ju*/T>>mg=w-`8?3q,6Q%U~u;!-,xia6A(fT/vg#7D`cdR$"SLbGAj=U8m3S3LAcaRDw]w8FmnC?w**;j86m@T<G>-aprS_=nRG:eQ}$,Wb3]99ZuK0K8NjTfCP^
-1;-nJj*F^S}Dah+NIQV@s@@R>GO_)Lb,JxC-5NJ$B!!(HgX?9+L*!+o
cL!+=d`jwI|4*Q#q8%)W6B",-0}.t*n:%K{X~J[BDO]?76v"<WF]MW-7,9Kv`Dpk*6bn)lMG_-[V6AgHg+`61X:?CaH";:Z$9OC6#X;qt@L;i]!l<w?g=5a/NwLGs<}OP!c:D5pz&r!M?KKQti7<#U?pd6$!aDuo8QEN@@E/?B3$yFf0^o[i00!^SK;u23#h;a3v<s#@1*-57vxWN=8GKpv[bxbX[bKcim4Cl6^x5qeism(`%2fE!(?y-QR4>O-h+M|6N=@=>pX<X4ji]*CiuJ+<pGA^O1)M_F^@Exfvf5O9vmze70-w$2[DjY30t&V$lFU-a@v9P642]/`0:e2AWLH,">WBrV(4Mfy^-U?35g`mp%c#fk-C^2xMyYIyzM(2qUH0VAnyEF1jGH]*.;{UhL*Ck[+WK9de6<K(vM^P9`14jYJIq!`V<rpdTMbEv5^?r"N26BN,XM]O|F3/+G~gW#60M*c/oV;Gmrjj.s}_GZO<vS>jhITXQW;3sjEEs`)"w,MBOB-YPT#e`%2;QieFa/o9@f_Z:e^utih,8$_$blXg#ijyn$)r1/>L30l3FVx=[?_rNH^m#!adUUYyqM%4-
}rZfJMut71sl_>Dwo2!q.eax7aqu[_16C#fv[<Sn/T:+1rBw^A8vUbVI306$aADjZs;9LfI:GFoX2tM6BWw]bajAxv7fWB>ZSd<Ni)*uKnlOuC<CF=fDL9))mu.A%b^3bIjRoC5>8!-QTIGo3Qih.,C$4ky^otc$%k9=?"[
tqu9W@$8u);Z?RkPl,#P/D4,m=V3_k91a@9p.MxNn.J)bCe=x#ECOc&,n;8bIT`plm`[{-(f4`"iAQ.-glxHB^6Mf;V.TeN45s9!!@Pr+fhT[<I?"%_NGO^F,JV9_p_cQGn)}f|(lgcgK#ISNN*m}-kih@$9h&s>@[DsILi>%hlE[`E>;#Lc?!xja#W%ccIYl5+j-q`I0UYO&jy
o[n]!GnwjAILZ&?>&Q?ic8Ykgk,LQJ^=QO9OtuW&:ba7/.lD>Joy8iB&T1w#y<Ew)*,t(=*tTv%Q{wN+VtfSMS^(hgBAFhW]x*E<jOmy|//G-asZw+_hlP^ra/GF:Rj]YM9x*NePo0dKB[`v";JcWy,C5,qk&>nVnEw61rQ60XOij&O6~fQH,2%f~3wn@<lre-kMih-f{[
2j84#F!:2^8aI(l_Z>#l+%fq035T$4q:5eX>)XM+Kw>(]+;3<Ty<mZP!R[_WN<9Sm;M!QZOLQsYq/lMu3"U`t?-dT+xOyEpRc,l1F<:ui)pi_,KLmiqz]2y~_sJ.-QmfeSR85daz-Q"-mQrobTK11!H{4(r^K98_-4BL9r=J(>.zI`iZIj%Lik
Utv7w%+9ANY9PI%BQP:I7mAl:QY;794(#VME_uwI~PbE:g*$K39M!1R=Ok;H#E/u`/2>.:f>G[^J6

X+0Up^7YBEJ*X#nG/N*j8&[K]htSsKk"!#?8cH486PRtBjb6<P_[>YB5nvW,1EeDD:3q!At<Pxue!<<C[9-`#c@0J.-!nN&@=98ob^)REjgG)v2z`;TY^*3IU&CwLms*-/%{"28)g1)I#nIepEE+.AjUi)&Dwd&|;Xuoc44]OuFPH+qw63:WSJF_XNDr*8:6vgXg]"
BO,KrW8;T"r,^5C?m"Ky7K<rV$6b7%sOv+mj4Ztp?:s[gu`>lyhMivIO*V`+6tj1m?wW0,~C=w4Mkx[uj';case"id":return'$Zu;BcrZ+&)qDeN9C;m`o,/9rNBe
+]T<JhG+A%A$
$9u)wfX^StDi0QU>XO)P_(?A|n9,fBIXk5xjd7(C`]j
>@PWLa$AIWZG89<FL&?0P%Z=s[ftHXT!Hx3xi)ZRYg/hIoHDE8.w!9CJ(amC=6XPJd7VF0d0w>|oG+6`%t.&lbh%utEaU!s0gy3Cv]?10>t@s
`>4
q<!`L?PPIX|,3h{,dmzfN;wh/2Tw}R<*%GnGSdW9j$3bjVxkLnyiut9b]Z*@R!}]Gp:sO#:w6dLDp%/,WhZKKy.bET>-s3[.[qJPR:a`-BtRQS`w+S[fiy9;;rxUOv$Z7^ctSiokXTlDQf7+VO*tCNdi4N2!,)-[N[(olPc1}l7,-@C@7b<L.PR5n0_L"Xm"LlTFbNst^uMmKV0FL<#_vCRjx]$IIfp7ireQ.5I]%w}l@YgTHqgoPJwW@esbkCr&u=EO2qK5{ip`sQ/$<vg1`xsT,Jt.mZ1k8w|
B6Bn4<t0R$n4[$5)z]*BS4rB#
CL6
UL8JV29tp5?W,9V%oX{A;"EkU`R<h9BcF9y9
o{e=@/v3yi(D-o"rDhMJDX.`pO%X9%0_NyZ1MVYdnEdBoiv^$L3t8ophFz1wX%(4Khr~I&K]wbV};z::"M8Z
,INMFNb3r1>d(gwXW^n2EDL0hPGtI:,Q0uw#x)TdE);DAb~<U-4dXWm.~"PaDWY+rbIu<nHqH"cSwPu2E..[N-Majkuul*XU,)adJb!<P/PPX)VZB&[exbk){D@o!WxsN$zC3e1gc(z/jM%7/O.:fB8-a>QES$#mWA=hyE<ld(V<Scx:x*6$Xczf<]qp]e62d^pn)L{i{8I37i$OS-,NN3ig%*RL(Y~`Xj%R5PxvHS}3/$z]gnzf~FNlepc->?B5AIcFWhi<@C|jVVIPI`]Gbwx8u>D1$-D".Z(-Udd:|lf]`mZBZkb2&@~":F];Taa7E&93b&1Ej7x>8G(Xk`FUjM4nO3D&VLIU7I7qanTm:geK
LCNc$SQ*I36)X]6>:.O]KF5"%b_.WdgL_V.I+VxCnhf/^x=:5kk4
mDVe??xiTl.SP=5fh;}b`)`"6xaQvb/GFn.a0OO!VI.#_=`#T>jkq.J#8".[ZnkD-q#v30h#zx)7n3I_&;exdBMtMVvCP)G>.Dx90P)OL_fL.f{jy[!@t<$[uhCvY1sEKr"2eoe_LZMh9D%+
b5@~VxJ;t0J,@ia9Ia[fJxZ?ih6F/<!E2,_6&kd.(cIk_dp.xu![9IdY@?Yp5uD,&hTGv&2"r0j~:sQH/brr5O6uqoLP2Pn:8-w1(56DY{]{=>Nh0N#`*a.*bMS9#j=8v38l[geTl/.hL}2T0dD!pWYtcWT>5@9d>Z"W[S,RoTTCl^l5
V?~Vv!]>|J:2HJ:"gs6MT41S{x(Il;P*nC9@nY
v_y/TG>rk%`MSwh6<B.9Dem<b.
-E6]{49:(4)I&dn6t)DF0<.S
(I$|!Pb:V7Sf0LDg(8Ru:l8O)y*KqYsx(?*/<qObCzF)/Lc0VWX6Zf]Nl~q^lz3KSqRsD%9Z/qOb8#[3@`y6#KE<=`bz:&QGhlQp33bZ=wfL[U8W(iLBj3Pd)T):SD:ePHem`Uj7i{kR8@RM&>eP:Y<4KPgqj@5^"$,V0pFxn_!qbD.fM4
8.dKBeVkKnqq`cuAa8$KulA@3KbhOXd.v"$Y^TH@aOh79(wxwk:+;U.Lqm_21K=a8WhSLX:o[dh8EV?Du6
Ert54a-yP54GIG@_^;=$Yg
^h;dIC~_5$*BjP&lY?ZPmU]J7,Th,(O5`>G>;
X"7eOO1#bJ/A?6o$zQ(JISY;5):?oI$vlB:pIq8s_].0C@D2j+2e|J`,kM"U1ACb1u]VQl&+pF0s>=-MPel*hm
"qd$xEK918sD.bo
HgdM?`)(6}o
IFwpVk-nP71M(~bqk)kHebBlKjk&_m(`o7qMs[?:I#cD
,S0,aHd[2F"73P<Ke8?ZaU&%V^,-E250QDGfhOPyu)PU_x@t@$UgvSb37+@J1fMCv,[E7KW82iW$TZ1h1xo9@!z$1r7>:v$pJ;:s>pr+Ps;gRF;y8x/`Bre<ym=:I323^MU
42VE0l-c0To_o^#9^5*=5/z;^Vhthvv>p3&@>g(ij.T?|.OgX3J9HFC(b#~(_x@NO,uira"m9#(T_2Y0_wL.E@&ni+.@.;ZK^K,3)A`gPT!XOgqX,cU4lT.DxexrzetwIIs!L5}py8/:F
"!ZQ&KA=LHzt|j<$q`AvlEsPu(K-l4=DXk7nwH`uJ3|:xiGm8%O!!Pe<^6x#[
U1C_DG0@2ajNA$#_wC(td#FX92>B&LvJsjiN:jjR:v#g6u|8sy#R6_"3,VQ8(yqT-_uCVx8o.S(XqE-S?bl/%nAx^ZMkBoB?:&4xfU#R6!vq#fH54$7>5=<F=rYY80]/EI_6r8mDWG!!=p!*q5%GXCA;o3+C$o-';case"ms":return'&Zu;BiDWB&)qDxdPK+v`o+lG
*pWemAV/?&2@*[..9HZPY7p[xaMrLW8D/;INiE.W%9)
D>Bil1pcK,XT^sh;
>aOb?b)k#2R0KO:f5=_e<s$AXqeAq
LC(w^d/SvaA9[u$@cGXaA32g)aVnORR&6Mh/[!4gQ:OkBl
o(>ymlU#-h?S3@@#/#_R!wKZL|[xphR|MXDnlZyPpYyYx4)XITA>0WqRay
COzvQ($V#j4_Ev
4Vu0bv%?#|srNcKAVq-(vRkL#EM5MR"vY(IKo]DT!HpDVE"LIm22LMt1f2!<`*b6D|ST;Me19$=N!P(^lvKxIW%a,9=E099I6g-V@@Mbd}YZOkZ;Fx+.b^=/LQ=PRdijULvrZ7Db_7:f54esII#9&QwGY}K%Qy9V61&R&g]NV?V<<13@G7%^Qc+BZSQue=E9g~DI&!CueMBy(=AZ+Z-ClW;FMNVn(?!-?"$9IR[Q^1i$i[:.1vmh&d=+5%Lq3b#GGJ41!&HQqs$Y8h$Sf7A6:DuxH`q]`t]5i@X7WnI~)/(uq(,MLv09[X[97_3=F,6~EfH6"!C87Lw/:!f5e2ks4y7j5h%L8wP:#1U9lX!u^J*3[-UR16NzmqP7oO>CguhyU2[e&8u$a%j"$ZkBH|wyAHtVJESGOg7)[!WU0UwLb9U#EovzH?nS,x]m05^R"c,.R0u}ali
*8
-)`@X;:gR^xyCs%$]2TEDtSKCDUx#1d@UwE@$`P:WIe6Yg!#2p:myJ)jkfay5[Zk_]6/i?!/pKEz!f<tuQV4}IGPQyrL
Wm=X/
.J-gj|rG@)Z|w6*%e+Upg^*8VaO[1E
]E7X;^>n!6McwF*2.CL?zP]3J*oyuF,yy@nr..
[y([K(E?_><X)@.Z,jfxVpkb9b=k9ysX,W"":nApW2t7"C.!OwkU_%]I!JJ=-C&|N1F/K+T"D:[8BPaa+w0)_qD=MeiMs_4}PVvbti.Q6GH?of+{XVyp$(),dll$!M,|!"@=5UB>Rx8tNAp(Z,%A=uT^&m!oto)^iGP3$^/fvpRK(f2?1)?A$G@rmH;(*xymfMq4*mV,Aw<>m#HH(^+w&o0`%oG7"E(,NZtdK*Ua372&(?n/^PWgwiT0B=(|^kDYL,D!`V<TD_<}^qNO_"_4v0`=M_h_nrL:FU.k/e$zQA7M%[l@O#3pPXOf.
$I]o8e<,5y*"c~.R!rU%<LpTtl3w-,c,1f8;u]%Y`vfCAEGHu}x3q@7P.A%%I
F#]Z[>?n0_.hr&4=wxYGP"rZDAK%s|AS0:3!lLSqTYBw3j20aE+odYMP<3A9.x0,27#9
U<Tua;5"jii4?kmH&u_*QY*!$Ae5^K$&m[tWJg8G"gNd_(<CAyzAa<:IZJ/;2?6=%70fS>|5?lN^!&AG~FMx*k.x*cz44Ulv##XiyH[)uN,LO;.J<@a$t>D`(P#OkWsIAC^<arN0].K/q(+EIwgLh/=?J%E8:5,x2-4-Rry2<K$bnGdDYVObny?l689PQ>_G%<;U$Lv7q1GnOACZ$]jU4kzrm(2HZ7A"s,5i]E4M)^5lwZTVH7@Iq)z!stB
|kW>q"a5@o4]:4^.W0s_FD?m2t=6rAFqOVW6L.=mcMy8N.4Ddpz2{9Euj&:DDM^;1:
l~Be8:@iI`ts<Hvf%S#$_jtxc@V]"M8jV/yvp]pwNO<tVlR=*[5Jce):""E8$Hh7uNlJ,AT_DFjE9[I*%WGY:W$i7N"Xx]Ih
V+HI9sCnx3-1|2wVddX])*&FVhK?JV~02JK7X>V>^?LPRvoDdFk+qX2`>0zVQIf%_W[CVCItuj`;r6~&(6#!{4nt!=(+uqaN,
dyPeeX3*bW^<WjCi$h0
D6^RPD]
roxrT@Ju:3=)OCa_LqSO@H1I~Od[1Zj&OamE._eq^_)^X
p(zQ,5MxRy&"Mg6@vu)nFqA#s67AHZN1wW3Yv6#be<1477<OW:wYxXJF8-KYni%BA3fDuCOa1P.5.ZBDwi.m1.0E(^p-(0pCr?aRa^H!w>8RVt%oLPX@>6jDj;kxeCwpjN&S$g+gh[N)xwm0Mmk"AY2;xp5w=XYk9GHj1t;1Fc]1z&8ul)Y>.<NwGQ]&co[,L.odeg]@6g6Vg
m
(G.N4.&[-uEHp]xWF_+sulEBB?lJN9;Z+JQ5ZjcMGVj:Xmz>#LKVneDgI!f9tx0/z4Mkx(z_z1Ocr2BjEfXu3BM$AnD[Dhvw^#N,SreCsYrm_C(2s_V?4..[YjNxH3`<&=,>:&&qKY2M~5)@#v2t<sA+F8pW,Y}QYB~2ZU&&ao>O4F8YwvTamy1wy=j*Au=&naS::Ff14lr4p%(N}GlJ~rE[y9.)<h;8I+yW*-u_x=<d1e"BWpt>M1S;[2cr4?{T+`[;@9>^D%YwiW5@:D$!"B/a9:Xb^G(V"@.,;M%0qlx,9Yx-&jC_wOGA-X(-0XkOBDn$*:u[B[$en.g<>$dRI%xmxA$d!"R';case"bs":return'&Zu@ibPDI?T)ni`#(-#/Y4*D}S]2hq1=5eQ?2,-E5oq]l$~g=NrRy#TwM
!u[x&75TWezXr1e8NJ`^VWPm~
&gMJMsQL]kdK=KiL~K
+WrJazlx5uOvo$u;;J?B*``(r^mZ7Jm[63Xvh1WLFIw774fPR~[lup[[>&mWrPW06D]xCr@%F/v0KQ>_AxY+]NkyN$F;=7WWecEEa55Lk0XY4O0Qh/[-N/Lk5/_t9=0+R~t"Q`dSAKs:DPh}?jgcgu&IaMM;MB3SJ>#Rnvv77
KthWIc`O1"p/@$depR^A*s5j9GolIk@KBJKTq{s2s;t<w/-_XRq5UWceft4q+u:sJ|AVfbaxOdL?;Ur,l!([E5^q#dMYS9Qn&=P;KA[,+(O_AdgX`"#_JF+Tv
mdyFoimJC$uo9ny8Q
CQ.?I=)Vn+pK<$7|6x*])pq/6{myil8Ie|bma`*OlL&z$|I)<J8Pa9!7[rq74%cR&9N;UN#oLD!X;lR=.:CiQkS)yS&MWitO[=1[[krCBp&9Ot!J%&7Xl[V"D<SwA?BDCLy{%k*R>-P;h0msJG>|%@"S=Hei="?#_>R
oh-i20+arU<u;}h%Qs)ug,u&Ggx#s[tk&x.qQF!GIlwv][v|
HH?1d^
B5Z!lj#q,UU{H4hj9]waWZPGSZPzjLHo<v3v3Y;uliR6.(xW+WkT,$M|7,I82WtbZ++1C&M1Su8!A6+?/.9M&%)>@sxgb3;,C:2@:
J0>#Yh$asnq&[`2)f3-;dKV.(8<1i1%~1|qHg8l[H_-G^f_RZ*T=lPEfQhIpq6)vg/LZ`O*+.8;BK-iwR"<}T5icWv`|P@fNlX,qJAfX,jEGA)DQn7$hPF)f_[9UDEVqZc;o+;8=(.j@#nfoW.v|Mgxx3xq4[5q+V28/I;yLxZoklWcXnIO=,QW@o)z#tG@FkX49TUbg&-k$(eOC4Mbu-2v`GXJ1<O2FBE7Z_]m_VrNP62>nK"/MhHtFa`b77dCtV%2Nmq!m>C<X;H"[y^-9;pLj/nD1]Aiv$Y
`LZNi/e$|S!g1
)k2[8YQTM@E>)T:ld^6`wBb%iSq`4u>Uz#E"rfIx]l~L$Ai
FeG6g]BATyuI/6
s;@:+ZMb&Mu/xNbYw458+v4a!AHu/mPvZ/JW7svS?WPs8I]Y*1TLTWCgw@<8m]TxC@d)E+"n8tj
=^KBGd.5Iu+@cTK47$Iq.s?THgirCH;J*W[FL
uxx`Q*^bbJJBK>]M7%<;8!R:v%ovnNESo_Fhfb_Q3wnWA^,iyjo}A>6W^BH/-xO?9MWDFzUt
<(<E9%BR?F6I<!F__=$lgbaU>jf?0<80jZQ(3<?Dl+:e[)(7x.Uos-ryc(JA7<:AIKe+AbZ+_Wf7T"Ig@NXU+?w1mK=*{D0)pmU][O56caz/jWwm7s}+}6?1iYPZ`M`Qif8lXQXcpa-Hh@g,SE:q@J}&sCs"Xs(+7wt-4n=4g2xas^<cn-uhy/ZL0?Y3TGJ!s-{fHN#L~%4n4)=/>DZ1.L1WT^6=J_yjr>GHlCj-{:!HQBnFSqog^E+t1.`&LOK%L6>)/Qu.?TGAf):5g:hTEF=@3RI.C-e?@v~tbRRc3IIqCqrDpiXGTM{W%(}_K<0(qND?XlYs},WU^iJw9l27<4#^W/=kei9*"SAH?D"`5Kz#$txN_!2Hje]@GM{:Rp=be</&png/R(`xvQ"LV$U+kZutP2AVgGlw
MaOteR]WsA%Sw#p}1%0NHcwI4aq]^Xq5fI)>;"C4TFno5[g".5`oIKEM8>D"u"mqM@]xr3t3mI:Xp<_3nbBwlWM%w4dEZ%02z(OkT:G"]@*b2tQp]9&+l/UZ,+3IXvb]HjKuX
5}#usM&!
+"`.)qRXU.L
bGqYG[v,s;(mok%>$0<a+.x3;j8r"w1/{`0CFl)l8+DP58?(!D>5t@R*Bj=u5krkzA`*XCxezk!<ksoApOyR<]+geoPo(xc0|@_P]LJ<tUO%[Q*@^]hjUqQ
MRCE7H~"sZ6:A0nYQJ{YC>JSF8xrkF36lf4.JH^("Hct{;Ohfs%bFw4.)ilI#FoNI*:J}^-8G6qkfZvFwK1eCQK;<Q((*[~G|,6eVv<<?y8[Y?6E.[o`Kd5PVex(fqme;bAthWGK!cR(Z;JVzHt_>CBlu+&wb`1n/l<vsmlKl&#4UI!@_cAbr]OK6kOb?n,Bh8Nk>k&z%iw$dnx.y38eY<CxR5$Isu#MkmSNsY7Jd+MRY-pTj8o[r0grH
c?z0C?;q0.FA/rv<*39S(2j]D#R6,Q}/"?]`}Zt/On[&KR$i=lbB[Ux/Nv5R;`r),;jBX;d9a3Sl~IV0?pA%qpVFQ.T1.P5+!H>K)5D.&
wspgDQcoR"7sONQV
eUdt0]#35g][ge(3U_`h20cS#J"n%e/!4ffkq08"qZ:cCkJb*a1zgG4U^s:&EwpJ=1w}4/ng%mxp/pOsS8G{bZ&YX/a)N,1PQL0i:nyu0?HUH84ZGmhQ:hU6k6q"Vg6_csFb(BUv:.EokS%>vO)FcARRt[wdMxM:;LOt-7]*C{OT(35bt.xAB$W;m.`]j>s>]xHr$&V:IYjEv?3jB9-ek{GMa2w6)tCk:cHC)[jv`Zpe/cg>eXqP;^;=-TAhNriy@FOm@=ms"*PAJR+wwtOz#@fQ3SQEXg7S$+
X$Yr`q>u6]ZiA+Y!Px*_zYh]-e]1o[260JcoknT[}p2vMtxihUBy>,lc;KL*e_c(.L)(z[jXOB{@"Q<YGgrNGlCVz1m)T>w=vft(}dOPk<*LY+6:ETr.Bs)w-0,Z&LEQ2KJ/-0??hjcM<[EBDT"[%kqCIfZ/P-i8
QgKa@@f(a764o$@gyV6}ru[jY(OmnWCd0blZ8Vb/%iuui}SlMKdX@e=T2U1Lh~5@lzYi8^,
=]$fZ
&-Nv';case"ca":return'!]^@)bP.!;hQzfnNAm^"NT*ek;q@roXSUZ_#C:4*z/XI=.Z?+M|$fYl
0N)lHS
"Lxz3fiNnQR(,8^zF;I!nTopa{uv
6K)CbY>seB3X|.<0up!@?%eS1`jFi8uPVe(53J)sRBP1HX/<GgChwpk9Kstx%F&2&w=C=JEWt:q6
;;=%,2l[cx><L2J*kt,gk7JYg@/z;+:Ny65{Sx,oUs]R1e
)(:?v3#0}7jX`u|%]=Fl8WfqQ%>FT6"E>MJ+GG}rq*2_Ycu-?w.hyYBWnu6@G=/K]G-4nr-):>,YlXP[d+w0OaOk~$<(Mm)?:p2P50g$l$KSO*ff18<<;7aT?Sdr:^9pJ
4C6YH+VGP`Vik>Zi63qrNASP*aBHpS0,W)@i3k{GvQHHAPL=.!AIM`:;W0A%z1wn*-QUumZD$xSZYED#1mRBV841z?.fUc;WAQwD,ar>y0Ha<4-$&E:Qi7@?@4S*!?v%H_0CptkM2G=h#nrEFl}.7LI91"n%@BbuX#j]_BqN^8C7rlHRL-S
V]6eDM1M)KQ-+Q~d_p21(Lx.A-
Ofns#s3s^}I0Q"8z6XK^>-Z$Zun"Q>g"j:y2J.HfEiEPC)PGf#YzmAY)M9*!

O*dJb=A{A*E4Ec]yn__5T@3Q4eyJ=N"0Yt^3&gYxS6aSS%R<`biP%zx,OYaE!^!k[W6.jDhB*gs;tTR"OPa
!]ZsEz3*7oO:4(]5-0Su-&VEpU&bp6({>ysf!fe1_]&hT(e?K!m(##"vY0FvVRKX?l.Pn*GhJEp3=rIeNhanQ][5s_O
S*$R2ltdTysqh8T}S*2`%qfpmjsUi~,8/[i&:};~k=+oupGbugPD%HYHcAA8?y3TSSd&y+yVBa:P"#h{r@Xly%^]]jkGLoV!mg>gpnq)/i+:1ic7lz%Os8^_&aj-:NtO0A1=8M;`YF!s=I%FV#w_cju0#k4a0E?#HvD{0*UH^8FlR~;8E$p6gm5!0%?$Y<2Sg],e
$47,Z+/0nX#T)CHrRbDNT.&N11_Sk<$gYb|sTfjZ^Ex6+$);9!RV|d%dI=*B:>aj{G1a8D<>xr0Ll8:Zr)ED3VE$;Lk[l
o9
JI)67a12C93dC;m$E8E9S~"1%v?FGui|%v"6)oRmSNu,0H7Yx.P/Q2KTSf-FT8
^y?DvrI==I6@8loyd90?Eti+.;uh%P,!Qp}vUF=!v)sG<uxV!k^NnctHafzj8Q+X/=%7J&?s3tLi;TFU|mz0>q(hc#aH8L5O.p;2q,b2Rj0[S1Ae+Yuje$)[%F=oIx5L{cJICiTw"[oD0?h[us{tWjK/]"2/.M*P`"pn(,"+)7EEec(hLbnQx`Z>vdkiuW#2CP,VDku>zB%!ZLY8kVI"~G="FV)s8P.s7o!X0$0v[Z0]8igbV*@"10jrj].S=)duQd6jasBITVfI5`*"vD{!"U(LMcbA8BOg[0zD<(olL]0.3CMY(qm`txl"D]efy4Sm.+Z/JFFeD5Y>/;f?5U^uTXS31n{]qbtpEkM#;E[[{P@2C3{Hc2sC*st7/6i;z2g^?w#".Vd]MUaH8iFg<xd7hgK
%h+>mJjwB7>J6N^E|e~F9:LetpaXvL;OZ.,8Q4:x)QLWULqxdE-
db%oZ.+>)K/+OR!4_jhTn`3=DWISPN*:nHF9_k]W:[N]oO;c>ys@W1)Ri/ix(WwsYwI6H#x@5%/]s=dW%^hKjvgXT"SS,r|_Ce=UOsFz%-b0WQ1=m61he,&4Rq/[jAVL~2x?N@RR?;gs6B&mWEEE{mmJgA4O{gk_E>:RAa:1#7~aLLhR=1E
j<G;!v;1PWF*y"1DN,#$4rc<`Q]S1dokd!h!;QGnODU:M(*.X&weE&PQ0<~D}?EO@8X:@y[1`XI+Y#fIR&=sw//sB9Mwas_&qp&c"6*IWLQbe/(&,3|P<1%-M]ZrK=C2WiEZ(S/<M6Q({d?G./OG5FIf6UHt/$-uS-U`YDw&A_yo%<W"9cl_u!8[Yv[rrn:NQyl^Y*j9f;rN^m-x<tC2}],mE%9E&L~WukTTY^uj7Jl6N66=
Wn`eeY%G2Yliy~ePJP+RZ1:$3{-tM"T(o5ehuGprgfG+Vh5DY)_"mxurb;,OQ@tVc*!XJEW+^ta~8:HXFs>uBLk/]eAB#WlfIfT2b`6R%,WnXRYLCy.&HcI[#oKQ[Pm]:OcO,sI%d_%.VRP^9URQ83"6B,h#L[^
QwQ9-SEi=FjuLq7L",0s-@.7%/(DO~%+6q(A(v^1,*>Btf(*k|A$&Z9tjUO-:iVEQ?[Cf:W5#
m0,Vi>K0i.4dLC7Ed4wbaS0pas/,g0p{%>_Tn/fyLUr>:X18:,d?B[gzpx
$ma1nE_-%iDtJMg!&B-2?("F5"Dt:xkM/gkF^be$}Yw
z?rc[hsGnM3E9Ghhm%J::irsdHEl
a2oYZ9k"@.n_],:dgjmtr?*~]g`2E_Xd?d;e7[wl0f$#^R5p:ryw@buuhM!bcE6?prmHSe)P*%T~A&7Z24]H
#iWDB.(^,aB.fV,mkmy5YTtwwO$yS.vfC;[mY9qiBo&^]>nJ3Rk`VK(l~o",aA:cDX*58C$,I3XDL`aC[BR@^jVq7kGU)7|0-C6R|sEOHOjl=Z3&T3h4z6`CkB3#U
`Ob0
fAJ6oBA}*OMiDX#NiaJqshR*<n28OB^+PAd4W69erg(%t*Bk:o]Jtc;%OdJLG/:?0M&}
)LMq/I8HVeL-t=7u-#mfhI6?Y8"o`gBovqq3;qWMlk~)K*&iYIg]S[3D
r]8mAH
Ufp=z
[;6FJrN!4!5af
=Z}x#g6rRPvWO*QQ|uiPH*YwW&#KxVm(m0h0HoUoVX&?#5u.PM??DF9/HWoB,JV!Ci*K84x';case"cs":return'.]^@)bTDI@W!(ib#<3I-!so3#hL%-f[CI@Odb>@hW;AW>P*R.*FWq7yI@CtkI"<&oE,F./6Y%.%owGivJq8(l)uF)#x@qnrs(SQm2owA>Qx>2aM9ct,=0J"h2564Sy6;@<Xbwb~%VcG8oX,ButHegu+PplJB#Ya1+mQmqP7.13)7.[z(lVpfW:[cZD(xx/!d#oI&ABhS)AB7Xkdqof8hFqFGwJ5KOD69?.=qsXV]v8ZQBBAz#fjCQR#aG;,&vk$UEmxED[l&yUBpi/$U)Ja&oS.q.P|rrn%8i?*j8`CK?o^7bN>v,v*0[Tl>Yw#_J7>Uk2^:_v6tF5=c
0Ae$hfjgHJ[RxTu+m+j0SBJC&aAoGH_RLEysCsa8BUqn/C4[tSU}H_)M&U]bah=kS<#YY%8-t9d-As@j`i=q#b[HBtwL&|O}>s@+uH+DIwo`&.76_*%
[D:Y(}h0P%vSn./@tcU?ns1Q^DJ#?O+TAA.u,cvsL-K2O5JP%;<:<CB4H1flc(1d[I8I&LgYF<w4jmv[()FuJR+l8uXpNF85gHI+3hvZOWq,*Tilm;#gXhQlFnV#G<%NJn+P"HjQjt6|+rLAsx)Uo3EQ#b(,pqf%6Fiq_pu.BnYIm/X7F1W11DQ{Tt52$b`z/uQub!k`B@"1kFu9[d+:Fq`qI#GC,6fx1$e-R.#bS*U-Ui2D?qH0w8dH0;]vABOvxIZdgus+_boY8|mBC:MCO2Q_*`@)TF`AYsUD59p;slIXxmRl0:Q]VSet]USHB2H:b5woFstnWL]BD=rX^*%>E

=]LZ6cf#w/,jRI~eC(lTi8uP=KRoI9{MY$5avQcl_P8I=&`=WAeIVa@Ur;!"vkc
eZbuE"77y06tNTmm1@"6+<dr8;|">+TkJ>+D8dFc2P?v6m~uhuNv4(!o?WTf;Z^Fy*yf,AUUW.{2vwQNC,o^L>V6{4f0Gqg)}
+>Y$(=fQemBaoU}W=jb-EvHmyo%5$DtW$A83o5p7Eq"1^$_?pb%>s^>Y^63&yt7Qg.MrOQ}=>wZsYuu4>B6Qy=T8(c9UfU3SQ
EG.9h@-5oarG`#egIf|w/3y9/?o?.#rr]4aJ|,@JoNOo&3P?9bu([_h,Nt.FMk:l,.m0Yoma_t$JKX1mNHJjEn:KFrRPAhW$cN%fkVgBqlGtTcCet)++]RKq[.O;wI@gx$>hX&]U+YCG[7@a9P~`/?j
9#PoO=]X(Cl,DV-yoxoVn(vA4:l48(?(f"ZsOu1Ehy/,|vo6+Vcx_/V<$oMZ;&{YUH+wX!g$.n&0lMK*co#8G*/+(rOss
D?S
8C;>f
IL,BM
s_zkY4G?fZ
ZW9B+r%KAz6`RsRg8GKO5ZP8Iw;Z8.cr&ov`=+&7F>KV`9t:_0A2MvL8r^nJX<1QYOTECtf#Yy5G&B=}
A-k4V$Y]CgdA[ka]}3@B4c](`iZ?&dXmlnQ)Be/nuP{e1i9bj$W]_!e2{QJAq13f
l+XF-`+AoPTJkf)m>3gK?hvt:Zl@4/]<S_nA0|:,TpqT5VmXa%n(/))>ngj9(_^=YlG?"ze}(+C{xtHP-Ea&xpwR":_.K_1+Tl6h#%9Smm(!n5Fk0:G6>s``/I9+J:l!d(bmhFp8%TEkvnyc$E8?um(w8-?=H:Yw^.H5k`l]*YO3aBx~n[(eL^Oq1BEANSoqrJN,=}/BXX[j]PY7L+21.Wrj?)7-GnHz8oc?`nQsCpHNE9CrsQ9XQ}ynOX>73xx$"Du6*rf"k0xG8.qqpJ?5(uWW;0&cR7>>0ljYe}4S$)E1C4kt>xYhdVE8<2l=:cv}S$H"!we6P_&,T*0eYGCss,:LX,*<0xH&m;_<LcxfCr6T4l*G
u)lQX"$3-Q}NM(v95C]!H7V(V.+a!x8/i9}GuJ,$!Vz1:MR=a!uGdT^1VxN5TW/l]H_kn?i*5Rl1"^utc$_$2nfcLD"Ze94Ik(+#FcSOH
tT`-k<z7mei9*uJ8n9(<~/&F5@*")3SG;AEa)HH,Z.)Jg;)$1@,7j#JbQ?W1F&:y"@-b*c"AWMET`M(q_RmK2QhuoVvM|`*Xx/@(H!X^/5cPU*r1
Yt%$u*CSRWM=P#.YnJtxR/J7bm45e[e:[n@Xq{r_0L,Au-6>&Y1fMO,dPI-s@AC_>6Evm%Dq7bDa`92~x[s{7=WDdmRC1-JzLMZl-WssX)h{EM$vM[DV]-eoUoL90
09oS$,)y#yL^V-y%A]LKx|WH[L7Z>ljvoDi+HVSI!&
cGAZPf=$[uZy<cN%Z<bLTXsk*qrc4!ahbl^.<;@Z@^>mo(z,$"Hb0by68jY<))VpAkxTO2CYk]TR%3=^&1V-!j{-5BU>J8_v
$;x.WZmN$|y8/P=J6:942yepU&p9+C
yB^R
YTEhe"GP;L7N:80xDR6p&y*-n}Tr]$cQ<"pK^G
I0uLFs+
7Br
^Qy"3s|<l)tk4E}nEf,r5]TE/+A!eCK
6IlGZpxoxvR(m^ZFPuk2-=}JBE~s]7:2kOsOY*=$-1x#;k?b^/|3u.]!GDQK7SD5ku1CU__69t^tdGcv+PNjb4ZO)l"/UX.jkf&4ueMtsu6#z69;k
`ffB;)#)Cq.[.#;ZS_[,Gi=Q{6V*zX;%1T13ZYV.w;<p&EPskk[3I
q]"o9cvto0,lrP<[,q"^e,=l
s:&Q6u$J_Pkjf.C?lt)wDu)jN`^_2df9#!k=L%-*5~NLKw,a#8`sWMLUBUP)v<]uK94&J,>,)yrEvIh%8Fbj"{voejML.6gd]Zv#d;xqb
GFHT2U2B.Gdf*,AFgVgWaR"34iK3aYPwJ?O*l8L&;;#y*3ATV$
hq-R=
V^yxNPtm0!ukIEU$=?v(8A{EIxy[>7
z$&E0d9[bd^;maplo+8]e)NKV-9C1MQLkEZv?4A70h6X/>ma)Hj<%R[ruk6X[CA-i0lC.Bv-ip20k{&3>bq}h,lYn$CzBL2`,?eGB/(-ZpiT&w%u=pEq]xoNa}UlwfdUy>Y{x{Ze--0cjM(%sK/{pKVUPK/Zh1+X5gTq3OUDa
P5a[?/O166lqxdN&';case"da":return'*Z}50bP.!:tl<gY:.t/NRT"fpE{dZhE_j"Ay[=o.{+dgS^pfpmW
7K_4E63i=wc<"i.cR!((@44)GwWxt3cMAqbclcQ,"$w4JO1`b&G,mQtB9/?K4kF)d5<_WWUxR;b7|$9kHcr#,(`PMqI+lLKG^SMQ2;xX4nYY9?Vbyjj>mcSV_uA#<.uiw0O<mT+ZH@A9<u6[jz(MT9Mwd;Ut"NLw+MWaNg~"Qr"
v]-;CnI5&Kq#61mN7lb6+kwBi&lASdqlq<>qhI2rCif&xPjf"4{^C_jX_Y8P?OTVh0wrKnDqDO3a_R34ROosP,?TK]1AE:`4]jx9@%Y5EX.H9cpZQK{745W(`!_0E*P+e9P
,7pQ
gneOfBYAj`6;D6[imFPL+u6];.b}1(I(.sgNNphH)Isi/vliE.qVYx:}Bv6loH3*Xx+D/S%PF7`aUkTnXwI1;y({,OHiOZ0y9wf@*DUP5w_"I:TtXH"mn.C?tTL=B<
A*teo__L849xF(]JISG@:u53=K"69PJ"tU0r8Mu=sZARIN|V:`_J%E3.]EA%!Iz`vE9$o6/FD8l8%?LB0_-
{_uN]oqc17X<?r6CI<@Y%Ug#lKKl^h+7"ALEGN+!=-/+
a
N<"q+#%/HFg.jRi!w?^)V35Va%r|a1)bFE$b3Gesv|@3d~j@gs
hQ=I.w0%B75*UHo?t%E@RHdG!t[bju[k5bZ#&.sZ{kr4HR(]xTbQmwXjmjXTB!AV"Y#OeKCgHxsic(9MuItY:`i+@Mz`_+[,S*K[n*eQP.XMe3S%*usGQqu5X>`t3?emS)RR*/t@oAh,-%yn@!m5-/KrT&pB~BS^mx$+{FoI*RJM$Yhb!eLHeNo*3OM18s,bK;ELmwJmE]kGx17L0
O8qmt,NDF(;9jCnJ$OYA[6LP|7)RVdZT+C/s1R-jybDlj@[8F-lR"z!qgw5?<-c0ry_MO-3fG0Z2IiOxBUepXQ.>`lKUStVB,08`-:4)U&?qB^A*QeMQIWN[
M1NwRAWLkS#"1$ft?F1T<jNS6a0ua2)Vh[]vhGn0*)g1pW1T%:Q/7lDKca7vM"pt2q-K&v;&8W#6RT/G)CuqO/B#rdm:c.0O><<ub@Qkubb`5SfD2>Pn0[/yn}>eaVjYS;fB[v#)E<$zFjOq/}JsjyrAZM1y8kEBP8-Hh7LEeJ"k/h"Olh
E;mF
dBIT!Y_/m4Gx"gGjY@`o>6`]/^]gcdCgXaRps&Y6jcpzqcI(``;,OU1gE:hIFJ_Xm-n++0gV4RP|m,
h@.GZr4D/AQu2;k?#ZF,1Y@bBsb?><
ePDc"G/LfQ6Aj-B"GWAErdgFtkkZN_jP"I`{(+[aZNO5&,73YepRR;4mI
BM%)y[O+sO^nH*2J7n"+Qm5]r~^4e;Z4Er`L:2)<g]^BDXU*9YU~G&,
;!Yzi#n
3TU>13m4>%f->_TYO]1F.YxtPF>2SaDu=n0
n"iv7aP("`af:.runCSu!?9U^E3(O1
)mlaHPeA%cU`#rnYHmp#[.y*1iN9RH.4Ef#Myv`.QFS^*77CQT>!aL%:jlL00xOaf8&S7=eH6YddPJ~A$U!gi
,fK_9qt%%(z-%&[8/j07aYU2
p#ozX0C?Kk>Tg>+Lv*4{6`Z,mip)dhLkWziHjBG
Ng:tKRwiw/>Gi|h(9D2t&fuimTmp4s1zNQ2+=)uBs??P#55mWtC
(k6B+@QZk&$q779|QMLynNOjoNZgv3$>V`6Hq@+4:Xr0wkHHbHdb]2?bfSEGv_E@]J[W=$XD8QjA/Q#w-Npss=#_V!CXk3yU5`1fFsb](n[wu-
]B3T(3k2W#T7Q0LD{Mqk<crE/H~D.Sx*<u/i10Z*FcHoFce22rX%cxv/eyB6ZRUjz:^
,:C7LNOy!y+Y,Vc_EpE.}#Y$Q;&G^cNi3tD8]-?4P]`_<G0D*g(=(VU/FAgqA$~x:;%vOQ0x<`kh}n1ow=Pe0W]
y*WbYxbs>Y?rFL@=lP$Msg}eSgy3tS&VEZr^JYf;v7YI{s>TTj#ZGtO+-E[qhi|dl8Jj|=iG]klE}fg.#KZAD5CxA!8sou9W+s>eh7_eP27rqq_j<uv1&Q-ULClbC"A4qQ}4Yd)p!x))]1MJQO434SK9Xpn@YE--B
>5P"re_4J%GquF~(&8dD;YzXT]49/m<"3?^O,LXN{)-*lhZ_x/sT1mx[F`U;OWIRxq<E(4krKL)p(w`Q
#cpd7t_`"iX7!9lrTQqTk
N*GKrzg8HQI3
QpB$OQaa+kIJOBZfM
yK=^eBWq`UMf!hX7D7+vtEA.b=ybh8(Mz;)1/V(wi&)qK3d>j1A%4yh"#f*;u$_d>c6E>u
(4=g3qR3QEX#r.[7`:"Y@!5bp~p1[*cbi~tO>{8+[Nrg4d^e%-T/?7HP4`!j.hIEal!Ze-a9/XV|QPg;<N7$IQn!^5?oOU;_=z>3eg+)ZUs"H~6Rp5;&pn*|
pQ4Shpce"K7LHj,/ZZ!cea?';case"de":return'*]^@iaLZ;#=w0lP,XN!^v%T3%o;=d1_XM`^9[h$Gkp<;Bk}J#M,"G6zu>2Wu3X_I5rHiU;$Z_BqKb]SqtfrYPX7It,&b9@+*M?/bY5U*m6<lQ=3ao#IJFY2y

k)]Uh-Qx&<%=0UTEVWeb/<Ywaq5G8bTApR/9^WVZcejt}l&@qh0*F;3^;fiM1*0_Y;3w?#$H]Q^UTlzdW<II5(L/OU527@@>
wG5+gj=MQ*vJ--2vW$acuupGw@z$U7Be.WLk0Qlb;TE)#y
Sw%j-=]
[F^(Gmg6jfx=...Cv
mK~XG67#aL-4Fa}YvI24.#p5"?L_7n"/jaG3p2-o0]c4p?YXc:rHQSH/YK-&T_j==L-V;yqugL3jJf8i1(kmGEWn>z)0Ii%Vjk*bh4Hy>+Y7xl)D($f!WhK,L.+_LmC39`W[,v};<g_Oh<oM
l|B~=F""r<g&UkY}aN[=mEdtK#
m9{*#iyNknUU(%9EHZAVVqMAASqhpe(6Mb-MPAeN^^%Y0UOousNr#&q:Y5NBx^tBIn3R$hJ
-k<Cd%vJ%g{s~
696ydC8jY>5Sx6f,

CqiHr5hWl9>XWZg/on{V:4j-rh*WVe6vs%edP<osW
LnTuoInXl#[A
XHi=(Qhc&PoE3H>05Dp2/3C2Xo.,`iHnnZexZ*)VNRs|y#5ZYvA(.c!AnS(^`uhWCVA}D]d1cmKpmV_|#_4(ye"v..Ygo|z)n+rM
GEFI~FS-SbYd4aMbjTa%D)!Bop-"lZh$59kC9Fa-v^Kaj[_1(`i"wG"goKn9o02
zn*t(op/Sb.=l$7^{_^H3LP02lLLu0AC;%Ep:FRlNyRk#6Vi9t9S9>uA;9HS_V_loAy`drxe!!Ho2DcEF!?h}pU,]0I7-6%0[mBpd0OsdIMm+1#XzMa2&*Wty9ag&!hV?
^o4YnAa&+>PC4"+@c45C$szpnDGTO-Y]&J;9PJO.i=~8fRPu*)geO@`@2L|m!,N0h>:!#<&$f[LNfTg3rkDfD]h(->-oK^bIcfF(5_3
+3Vec&%QksIaB%lC3gxR@oSfsGu%q
4:YJV.ME"Bh.IEb[9yE@5qbH<OJ:29#O1F9c}&D]gm5^ynTG
?3p+Q+=~fwe
auQZS(w17j]
,Yr4F}&7pqR>?t,>]vvqI%Z@nu[ReIdH-Zj6@i0
&.QtRmT|<!NxB>5na%La!Fp`R~1q>&_2F[$&]wFuW!k0<?CN";V4:Y59@Z^cqy-xj:N0#2yW+#yf]2VCVp$7KO>XS#1$W=3bGPmRqbf;v[)QHgI(c8,g?*SKw<e9^vE&IG:&`r<9j,_av:qmiC[IVg0aTF-Jfq$"jQ$Y%opmAmr,
J0])jSfZcK#enb|Xv.lEILI?]HWsja|@j=K??D~u5Mpa6%>282UVq@.*.:FGIWLeH(!GXZd<H>>_U:6Gxg#(PH8Cyr*si"PHV_]0oJBaE9k
,CX->Sh2Z9Qk}3B3KnxE?=_"1=gHt)n.,3hN!U71j/g?#=.F"5t/QQ^8<E&qI?Ryb<-8^s{,y3?4M^Dhh[vhQr:/Y^O7%*I4:Ut4xKpTnm_>(_#+Pp/9=I9
mHpi0"To7jQ)Lu`rG0-epnk]/-%Exppr)^%),`aB]k]I=xuDUJIQwkTNdjN;Z>a
[Y*0yO405X:xePs<Xlc4T]nSSM;Pd#zN7A.7xv/[4*p]k/
Jj)qth;#h@tBWvLBdn;:7OLqwbdZ7y5qBAR~VM&bj0)U@FiA9,IsA<T|3`v#0Pxnv+*uQnV22TOPXf*S#v6:LE;d)]7*gTxz_tf2oJmG@nM~4mHhPEB,jcL%-5=FUNKpIk
u#LSN6&6#=KUE
L?1UA,Gb}?T(uAb-+$95"S
::^2/_[3P$lc!y(?C5s7Q85#.hn5U&vC3
AR:
NvQvGj6Da][_nx%
j;wERTU$x0g#VW;l*FXB!*
e%hvtocF&SK;t^hSTar.DR6IMN,SR)8a*?>L2Vi]TD|rBZiDo_0gmL7t_rbO,G*TUN];IWPgTA=nBrjhMM)R*ooF]W"$8kv^xGhQ7:ulDrnKyEl3XGkiG&~,#2P[`_GCd`m$;"]jq0AW*RP!
R:/x^!TuU8959Wk7pOrdokn?PN,[:f[K"8MN?Ek+.6tsr!q-PoSddivCFx]ft~mB8
C4`Ql"rSQQy0n5OpFS6?r^W>ct$[O5bIjB"pB;/dOlJ-O=T]OKLsegD{XwTX[/b0h,VO939`C@7z07(u]+&7/zq}bDS@f0s|EtyN9uY}(|
`:H
E_2`imzsntt64
S=<"OI[L6(}N+%v*w:/PV>HrFseA<Yz5=m)YDPZ@Ck_:kWO=GmRsU!"ya+^Zfkfj0S|=2g61M;::^0/!1u";dU54WRwUqG`!t:9fP$wLrLq@Pn6wBQ8y~`Q2("x]Ztk9*3=ZBprX~?kcn4Yk,c4_twByI_SQ;6;j"Bnw%_hHvjYtDMzz)C6PbSLc~60$9-7`i=ltno
>LAyT2>ZY"]pGaS9L(tH`<-~?6%,Vn:t
Ejw:6xQ]!,scS$r8-O4f<5ZH/xp]^acN[Y=s/PW!$r
Zv&{Nnj]DzC@O7`-ebOzfg3bQDAk$8p2kWXxj2%AFOq|_W-@OvHEt0$@4YS~#yZLYL;jrGLbFo`OOUI3"O;}S6<d/2)8p/we-KOdj_l|@>y_)U(=%sem-owm?!!iBCP~qsxrGzI)">g5!K8cXHRPN_r2EB2]_gUd=j:,1V%eSn!`W;-|^2@0bQc*Ij?rNjq*x8C*>|klM5KG=DmqC2imu21N"Zy59Pn:5XyPBQ,~G8PPHd*2
VBx={b
H,!v$dnK"zxP)AX?YlYiY&t_9(BM%Lg@RX7j;mFVP"VuqD-0,:&OP92u6df
niTH8Q)#J;NTQ?(>hKGu"rP}r_drlM^6pe%rz"""';case"et":return'!s`;:bSZ+:",Si`"*-#7
HdP">V:Ml
Cbgh8;ld?sDPOD5lk4foa27f,>l1/P%-]bWHo]U|QALp""9)@`WZb{t/k[
:L5r=bLZ5SK_@[&3x=I4+D]gU6(^S*Wu|YG.;B67Q9ANzG>"bb=$^O]t><BVuSdoBIRVaOb1#TE%!H!4f]2CI<#DCoaLY8wJzB9yK8VO/hZaESNM|obm^y#IY4_wK;CL2<,5`6$-wwLr9gv2r^l/~un(v0yS
x4khccy97p3UaS,4vL;t[rb/1ue-]1[cCzE1c&H#).J*Ivrz]Q0Lt.p"fl48Q+*H0qfeXa[P+|.4weI9"h*]Vz>H<xq
EY7J5-`B:Ob&xVDz!9_9)ku"jW5WaikF#|5lZ&_!HwiVGc]^eu)p(t
0l4Op2`00JNB$t8i{4
Wz"=g1wR6FTX)S8$[<v|m|!hQJ1&e:lov}kno;(.T2s6>C/q$hSpj._H*7sOy-u.]>"oN#N
24h`4$,g>I2.W`&T:lg<4,7/5Zc-,V.n?S!nk1lRNbZAI%y6
rA(gXg+?ck$cZ6?PLW]D9>i*5NP
JO3^H7bLvZL`5?)`tnzb>V|FO.GvB8WrMgs12Jj*d^NA}/"byf}K&w.fy=^#@EC!1jo?rYW9Cs?T*.{DjQNj+d
s+L[#op&"cUt/gX5^,.i:PjCOM+ivJwe)a8LTFgIW{Lrw`HyF*VGsS2>vz2JG/EWdpEAoZL
UysL-+,Tx{HsK9JAQ>XE4sIq;Fy"lKv9P#37Y@nJ/&rVs5nNC$g~5NG:1JF%cQqvZ!^j3Q&o&-=LewSM7}1S;V5@En2|9$`|=E(=9CURatE<Q2Z.*@oV?1"rwsZbv{dJ$ls;s5a|1zQ"(O1:e30{%28/!)ru3dmE%>6ttt<dn[s6?02zmH91GAe"yF_b0gg>Q}v/t."z+y+=Ow9kT)Y):$gYlcq@$0K"D_</c
Z~mwvkAZW27{
L+{D{,C/@Z#g^+_p#"_E9_Afl]WQ1/i_0VSS5Hx`p4_j&!_)6@
:LcZCk;^R9SXV(KKO??^b{5,TR4Y$TZwEXbD`NgosNd2e`n#R,9Jy7XrI:ayC1Jg=_!
LBVSH};Z)R3hu"
%Q4L66#5yLDhJqo`w!b%O*Vf_b#+(nrN=;NN}WoVR0N>"uB3<&s(K8`&id21[,5b:bG60G5
VmPWK%CAD=Zk,dpA&=y`V>0b^rP
Ds$nwQKWhiTs3vky`[*E~+8,i9LILo,DXF|0Ib,OL:}J,M8Ttm{!c1m+5d%CL+YNlBU/(os1_?~<N4%8j9I%rqRxQY5e)-S);o{Qp#-m]n"slTFY<g[a-0bg(E9G8:#Yk-0&h%T37c`g|A73:]}OK=N<^eGYc5+MR.&C5>1jBUvs*YBpsQ_ggT]![2i0ZID@aW7-IiJc3I9ag@HCb]3,jdsD2l7fSiLo}rk/i8Q8}>`XP[gO5nhv;Tj[jl^fLhaW]&,@WX"l|s:!tw?oENXCA,#v:WWl/Bj.OjgZ,dPcEbSMe1ifon{>"JAmjll):2q4$laGzJ7,kJ
>cE%&oD=Yb7dF?:=mcAXiRB3`c!5a}>&7V:i%#Yhu5?YW!M[FNm)aTn"qF4$;FQzPbl
hAMpd8D-H?$>&]jTi!%0SjeS=-OsMZF{wQ]SM;IU;lNG4/_,?Dnq%TwvqjVh
WAX)>_pN@jK4)aK9W&TpxKytEb0C`@S"!Rgo2-E+oCdV"opPL
**@Ldp%SuOYfVvqfcK@xSmjs@[H"i/F;THnqM.9oH^oKaYjXBpk:rAnA5m4qf]2Cvj(0RE8(:!@:o98UJk
/Lkzl4b>M~Kbb>AS;S=zPx;!<+hn5*JJ_9cn;NkGKv8XRTqa?6:]-NQ1@3KZ>1!q_^beTI-B`FSHr{O2H[b@2WqW3Xn~6H]6#p7!wm/.2M/vh)Z4dak&7R2J;VBaI<VC`Rue-(tYV>)g:?&bP&HDW};3loLaXWdj;?2@(
iulH[sN|6DpmN3NpySEp".L&wcq{ASFs<h3/X{+oW9l?#e';case"es":return'&]^@)bPZ+%fWLsw$T<z(6>*NKT$l@o|F6`mH$]l)qxgV-KIU%U@:h8k[{We!T,;HVOXuCciWt7xepD1-q+(]}q]DFhM3Aodl*Jad+rAmo*%I![8LLSM<P+$wmOe!e`XQ]TCgKp.@NhcByJQ"O`)ciwfP$"h&>1[*KBU%:trYV#rpTN~%Y(7Pb
2_:ar,UWua]rD+h*%)ew:5lW}l8V[u&>2%/]jd-=*"aC$Xk1fy>-j7
(}KtN%SM;`IHZF*ybGxQS*A5Zy2g]"KDrigyV
"kXw7$KpX/5e6{?:x:!rb6EyL->3)zR@Zfjj3$9lSayB,YTnHc4u=M30O1khcRp]4i2bEr2KPeSGJ>e?,En?gh9
sElF!:LohURmD.>wLX1uX|!lZ+N9XUf?ND34OAxkX6HWeb_%SY-F>GGo=`c=keI;wy*Q/#2Dy4KJ:{6@N%*I]h#<I]olm$v$yWc=#-:kfvsgO<SuA;fd.YC=@G,ri$LnwZLqS4PW6!.zr/"4VV!(U0C[HDCSJY658bRvor`rQU#]xPK*]&sAWJe]LvJNw+qd>./8EE?x_ASe]d=QZ89esm[g[~>#DYJK?@LQy2$-*B(u!c;C55[M8daW_5p1re-?YRQrML1]6qdE[5vN1#JtMyNtT"6[s}BJ
/4aUuCGU}TU<N
O<>&6".h~xZ1<WIvj)yqa0{P-[(.FeHF@XV
%2sg!&/QkZ@`jQT*sYv,P/dPJDUFo_@FmmlyW>MW{eod}6bF&BPZ}QA%fJ1&@D}RVMJi1?W<
)Ap+lfb3]CK0:YFM8OJ
$SHGx&_3S@k?H&c8x!Zpe;r3O6AHfAT7#1#~Q!Q~67Kfs[mdYx)D^2XA,=4[cJ1ON`vnI"[a_Z2?+r["Dw>oj%.Di-uc47;fn=W:V*^m%UuKjl<C
soDdS9qtG:gfjQkhG2!A4nDZ-x.3BLZ.D<;>Lu)cO@8.n]D-M4y_C^kYY*cV0
iY,M4,B=d5po=`$OsYEhU)_1rq7dBpFPV!nhj9HnVH:&_:t6
Xdaok>,_+)=4CI*3!]-.*UhU)o-zyv&[yT(vZ:T5G3
ga9yp`jl>r88pT>=Q^A(M;-ja+O1UKw!FAhQ"#F
wV[Y&&$/nh5B./sy5A:lA)8tkTY@_=K1{Uc
|HvD0hJuxneL5HVRv;Kvk(NH:Rqb0Zi5c8mw3sNqv(f%Ui9#gEE8{gOKth!A;6QGnj8+5TEp:/V-~8;Dt,mc?taHTk48dlE@R9W^{dCc#rP8@gf@^<1
JImCKhG9Hp6x&(NCDBxmP&26H
4E7,pBLWhv/_ATL%uCq;rs,f"Q!Q,IsvaOF-_^wEM2d);
`gwv%;2X-%&I;>.g(h":go_N(u&jLIlLn!4o5%wAg3*,/QwI9b?-;09h~Wa({v"I.d,AS(
B;T}IHkI9tktau,N&w^x?fw4JFcClG8>fovFg9[e<xKI7zh6"0b^>VRh#v%$E79oWRg*gWx~K?.n"7#?]{:-%%Q)L{q%y+aN.M<"#{2ZHzqNCQX/1hqhAa[T$hso1lqC>7w:,FF(8RC|meU{/xges,gw^6YUTnbvbRs#E/QLY~l2iLbgVGDGFA;%e%;[?ejydWZi*tVU,.bsb:F8>B6.D8
Ie+7$jGR>)Kv#G%r(htHF$iqp6f]GRZh[FggKn)O=yEbI=HoJr+Xbn5^Gm&?MK9vmUmsQ(EL+gs"$GtY_%,<yZ3%sf10(
SCq54Cfs+S4nZ>=CLU%P}KDbT6~l/w&)#DQw8I;VFg=:<
(5

_0E)DJ

dFX3(;<V$eK;Uu*ACIU1`BW_uSW%Wbk+.,

R&])UnG@1$wmu-kl?albwAK)s07Lf.{Fe
Twu[=s^Y4N(XHNlJ*7kJ5-0+Im!m1ktTyTX],[8^RRDm6-/Nq`Y$.]FDu8=xscJ>W>;<Y.i9=)_)p3f6LvTw#*v+IAaov8]
|J=qf]Yv/C>#UC:X(ZoB7"4tW5qYw2A3pMD/294xLW2Cn3!U:#*r7hj=QTMQ!"AUH
VWtWq+.0V)!yd]FtLSlcJa5WA%Y-pNO#P]-YAK1Rxn[JN^1RP#%+al4
YY8B/:s(#GE-%h9<O,@kAn~WTvL3}f3)jYm-KqZZx"^RL"c
8tcf0O{8dFv#T@Anb3jl&+;nbea6M=%v{Tr6+/.-4-]Xi:Sr`!|9nNN**
cHGT_]OTJd;LC,_4(i`y<YNgT.tb4fnQ`6pf
kIG.whs]OrX.Qv/L7_3J0O/3a{Cb0y+
`}b,Mz;-4?]")Z#wFW&m*UJ:;F*&&t?2q@H?C*[7Z+)e
iYxr#-7#laGKNTMvUYJ=AXZP7e;mdY,(WQ&JZ+O&_!j.|^M]
;NQEMC1kL?UijQF@YQ"V5:%iR_<F=Kf[-32QhXVkATF[
_-Kq;q{ln/D*7oc?WSi[i@fr9sMCfeDY{<A@M&+g!a_T
#IP}L`Hx%H>s$q0B*l#Cpo(H1ZNE$_OSI)$ttlK9-1];Q$^$n$<J[gk@dj]br%p:o7o4G{fT0A>iEtGEq%ZQDjlUgZ&0MFZJZ^uU1]AF"%cwphM!P;>l
jPWA5IZ)KF^/K1I#[@J
^Dlpq0^ygrSV8]y&4:Y$^ceX#%N4#"w?}w@>j8FSeu$o#]3OQ1QQ~s:s/Tq[v)5[b+R+>y2E-MhsJ.R/@[!T$N}LBlj_KPy%
.uI79iC4)M4OUH;_kb+jyl9.fN*3<8Xi-K?"giZi3orZ-8&.0G$`Hb&6m1Q>UGrXC(l8lJO-UN*@[T)SJKYcn)?=C?GSbz0.qjgdY
-;e<qP127*^+%%p59P".`2#PpjefIxlckH36De&3%vv2PUE^dD?mB~jr,#6jWu7ImH6C;|e~OM=]b/"iV9(>+op`
qjk^F';case"fr":return'%Zu@j6KWB:$^-q|!ZWa)=$#<Dj]TK4h$;Yp+(Zw;#V
#[B4=&;$X.maemsC_f/DJTkpyX1lEVjBR(E
-p)_1H(9("yNa[eG7IqXBUe[/`R9:Wlo0icQQg,da3CaYrJW9#Q$QuxwjxtG[i_Ld
$Tgn@{@M<560oqY(icf/j-;>#`GL%UfCk~bIVP[w<t)U

>)_,E+K
3j:%KP6
=6[HHW,6W1R1u8v[x2g"X{5$=WSBMko(umb`>y6r19!4tK7pU6onRcr%hooA`_3Xl<4KWGeYsdEb/CDK7?vi
8(xZ5eM>ba9o%-?J1)|OVC:dE_#G%(dVx6$ZfU[N~.Uha"Z&aF)*AiUSSCzy]wG["yo]N2RQ+VMoe1O
q?mm)A],u;UI,CGM6w1n`GG1g1;gu*8EOr%/!@avcbPSi7j/,d=rt-d^vrwk+wf7++{;$sH!Rh|:V/DyQcFb,UOhcl9Y_6?gAHMBK?(b=/KdY#vohLuseW;I;o]YA8b0CVD
pWZx/DxDV>O37
@s+)a_&2elpbhM$5,f,!G>@2bQDn]s2/1*TDcpF*;F8KWy7kuf=C&&qo^`v.I4hG>1fBi;7bAvdQ?`o4|xbbSc^GaGbs/4^5f+<09Ure!sQSQ9hg-j[t*i%y]DK>H!#q&&kT_&"cx2sXyQ<8Nx/`jq5U"UqDrg67V[0aY(H4,FiXk]U^*>(/8D*nzMc=cYx"|43%>;O-_$")R,WW:xl7R?<13Rb%]$5gCia`,ag9b;}9U:dK|^+W-AY%#ikLyr51Piuxvj~)|QO!
TtDW9VwVO"0ktwUg/
&W+.1ZN{fM2<k5>Zd$Fzg?4yWGZ*XzCsLhb~,Y!Ak9yqnsk-]J^VN==`Uea&q6BSw
(y-{+p
8_<?a:^u7l/ynW5p)9?;py&
Fn~ykmbv{`[rB:(K@DHp1B//H&)>/SJjI9bT_d>#XV?Bu]l4K0{r;9up(UIDMP8K7$OaR?T]4)c$3XT])#*nuehGMO0KfTV"V4Cd"bjPp?T=QGz$$],%<Wq%56e#OkeCjnJ$tqm!5!ldh)K+r^mfs^Sy6]}jp"_86^%=_d)b)Hlp>Ye,e6+v<a_:?Nhp}fr#"cLkk:jw@tB!))(.sJ-d1J:0a
]MwEC=mh6#amI7;>&exTAjj]+hhb,!D!ht51gYOe0F<=Z/7rg&enyd9tTH5PW!FQ1upa*yDNZL|<l=sy9O2s&+8i}2aCf<WZ^k!t[29P~
|n2*{>U;KQ}Rm?+8#=6S67/u0H5la.zFFKG1lQ{%_4=;!SuJ+-kpw#9]8:%fFNQ39m5)b$3_Tr_N18YBhRn(j>?51DcBO#>-sZs?IUBq[@U2""lj,hm((Q%,ClOS/_VV&/
M}j)<L08d|1O`]yd[7]Y>%dlQ`C.MfQa4pJ"$(aD8S_K?<8IH*C?a*i-43,HMmM|LeZBm5hqNZWn%[1:8ooACT#2wa+soq->8PNi+Q0*(&dZSP#S]U?kQ<o_2_bOrobc!Fsg<+q8lH*BicRCRE027V#jZ{Q(
CF_x@]U`i=y&bE4IIx9GBv-`^bWr3QT`MEy"!/[*i)4B$.kfk1moFbJQ;v|Qs.b>^(q`{>FkOdWZj;zHmin>|*n.ht2?V"c+M(q4=^csr!B!A&?
,o
RdQ2eVQ<FS".D*1@!{ass@g$9/kZZq)TeHAD/~S,N8Ki:P$]GFxeyi9cD*5+(}o}lW!y1LFhC`_G$.G0#Y30[>6as#*mhFCRIcTG[YGzi0J@Jevx6BTlA*Ut@Ft3`"(SC=Y=y.S64J6+oxF2ia#,vyVuZcAKT$5X-q.y>~a5F{kLu"7%ozo[KW+6M!$)@[@)c8G/4uX^L=^Ihc:Zlay1fGm>=jH`x)B7
5&.DV?o
%l#d_U^8l!#PWYCLbpkjpM(
+BqX&DzWv0u<h7My#7w1uGO48Dv6/Hhc7PsvBIerxZB9|FF)}aG)=pUx<`&L`dvI:3<RZ0uibfR+5Pp2flfFw+o6a&W6qrBOT*x#16_H<:&<="y:=#ghl!gPqjX(l%Ce,b/`6n+vTv1Q&NJq~;A4jE2U,P,joL|-QMe!AIQ7uGP*Y%@-w!-_i)c-%oYlv2g9?^_BLJ$*~8.w);(PX[YeFt#&c`}Y}Sy7}23OK`|B746vP^WGl.~CWe87"Nvby&WSs*mVC$3j#?y!Y-Ex~,81"d:Y9Hc][8#WCtfQ{vG3-_[jhgwHG
UE8*KkaF6qJ)D4Gj19bS}6A)GH!N.
QRpTII"26aXdr8Jq_owCX(2;Ddo,*Hm!8Z"FG&n7*;UwM&mfjM4Z<T<$]m@3w%te8[&J6M;g2F=P8Ok%>T*bUZ*@jpDJ}QW"%3^ezjY%WSZ+R4d17xZ9jF:4cEEow/}oWP/5O<vorc10./D`$t:6h"](qc?IA/XW?Us$Xq0s":%L}vN2~4%*
pzoS19K+7RRxk)dr9%D,?C+57+$4T?lLl#q#1]k<OG0-,pu8idD2S<dlczAA3W%iaCqAn0px&4iL>@V1_3
q
,,"@(r|`S?-=nF>Q4K?jgcv`?ppDVSHNbV>uQL}jlB]]m2(FG2nGMWsK7IDj!emZ&5b2DD`O<h&NWVu<J,?7zN|b#=ylokA,i(4Ukc6e[pSio3_8easjdX|,41WM)H[(G(KM]:,#Vs2NIV_X%RzfN]>QhvP%c[UyUhH<8B7I-`(O#<L*zV"r_-`J=ev/yITZL0sx}
_<h^y*Q7BFEI;VC3r(@htkB/"16%ia{?^S?/+(uS[@~<jBYZ5R$dqXkl]_h&ch{HmQ4S-s4;$,$Xc#,(d@h&e56T~x.#"RYR7I9y24QJc<9N=AU?8_->7E&Q~W{XiYbi_G5&r%K[|w9X$B#Ut(>wq3Q:w6cAgSHnJL#l~2hD/s?(O-rq3SI:9`hXa#!,0_2!I';case"gl":return'.Zu;C7o+>.@a.ii#!-#j+Ko:a`OVL*+6^_[H6J*G2`S[#"HZ,94QCF-IGy`,f0$LcP(3cd%(s`3sdFc(<F2frwX*wlh[v-}]bBIcZ]rP-+}z%8byij`7Q#{r"8P0`/EOkcEYu`_y$5}4tA6Xqlco3K=H!i7_fOq"h6oQG,T6lkzo^dugl;>Lcw;yHp2QBmiV6ICFDu~1M1>COs,#gO<Ei^i.oMi
=43_#6Imz]tb!l@.74+8#xc58HHg&Hqi~Fm7r43pVFnjF7@?{lUG[Av*U6meB/Fv(2H2Cw|jwIlYoTq?]Yq+xPBqr(pbCwzkA;RX&;):;ogyj9xV^VUGu)D,*=F6]H<:8&Qn4Q2Nka_Vw)2L|*RArx9v6FOmB`>DJ`$`F*,G,
j)f`?nv1fYHm{JlquI!O@XH5jfejDJK(1;.:j>R.@GI7oT/Wv).GrP6,usNHCIv@K,3aV+d8m"#3
I;`=lRWp2|+oCuaaQ-k4(boCH%mnp=R>v|Ih:i#IIybJL:8DQ?htM?%fM5Lj+/Xme1tA!UGsM&<&>GB3@%Y26b@i77+G,8kc22ikPYfg`!#o7k#.ndebqJ("0FJ[2U4ehnN8WP2!Fm57[}6|4jS&J3#yLRE|BqLQ@5TW(,W:4r=RjXp7b541]D5AZxZR4`rzmRTZSi;!<U<ts7[:p=UBCjS7bJe46Z#,?G:kMK&%OTx8sc-s?YGb*.K;B^QJ#,5{bueO_m8y&m:t-+4f&rZ5U=0]P|X-%%/m?7r`!rDs#"hBq>^-92:e31:2Y`WH]%cYEx9^e63sW)#e&GJVl(H,ePeYc|"la~O%#P-L]Z,*VA,6<EmIFmM7=9@k@DgELW!qs:QCyvE$ypm)EzMUouJlM[=9g,A$9aL,vgZnB$WHp!$f[+]qyO^@[Y?klevnIlB
V@m@O
kT$b<i9oX)?<6f*^TyWF[}i`xJFKZ`ikrh!^f;]tFG5@VPQe_yP4Tz5h4P.<=Hq32tFkMxYG&4?Zpao+E{"XY%"6RB>^8GHHR<TkiT
q/g]98`EA[@^eMWVD<=d_j^2Ev
jeDEz&Wv53-l@W58,)nvl!AHP<4mAe-GT{xj)Nu`]T5W1X$n"V79*+c8qk#A6ppp#Z52ZvR&`Zf]*U8K.DAZykK^?+^X!s5u=%Ir/|ct6fy9!gbHy>-INt<wk]gm6-],+]ZEZPp7j-b0e|/%/E;nAJ[G^0y2khl-+.&uA/Hqq1PO+h/+k!7V6S6p
v5"$wP!(y=f`U8NP_TpQ(u/G-
JZ&/W0$+WMd<eC,%kccPQWo;KpG9xQoS_(v1gLYoN36V")8<pyl)X;DeWBH8/=Zj}8Pxkr[B<Dp
EdR%=5_T}4Vp8p@:/&VZ
d!v&imlJi=[#*ZW.oMr_egTNL5RAwq>Ocv1JJXx|%p[[yoGei4y7RXUQ"lcv`:I!Z@pGkaR10:e`dw"T/5LxpYVFjyg(:(E??HQfu*UIyXdgntNNt`--@QP18S&VqKFsdr6Np;ce-M-pg}=qQBp$Bl?H^b%Y5JO`PXBHAV#iu+y~>yQNpvnwOj9u
WBs6Px?#m++2(18JZh~[v4H
L)Y8<Gki&FC"dlM(2R6qR/JBHyW8>^~A&tPJd4z0YSB9Gv0V
@ul{P2m3@m2];qIg>((q=^]2AjE`w;yCy#m_BYv@u!QeG#p*c4<FS*)zu}r4Iz#"c[M@K;i!6j6~1R&WwdbLOR13cjU^Nm1c:`r%
h?%sA+6,etu"FhuSC5Ge67wZL5dhQJ`Bt0Kfb/z:;+6RZlm#pLp6(if9sRUdP
)m|o!L215;&m(h{R+CZ)Gdt0>RX=$SLyBjeoJ_>v2R|jZAzq0e4nq-BSuUHZp1S]545g4&C&5,`4Kk>vi)xS{Cl6;I^4Bcbg)$M4GsTYE^FXRZW55i!UZ9c0Ii^I~Sbq1W|F3`RdJIxv$c;Cslmpx,T?q"bpL^aQEB&>IlgnXXt%/,>k+`xgaC1iU((>8N
L6z&6JQBaF9]BUL`^ANRC6dLe6a,]QV/#nZd*&4:dNwmB_C7TVI-?5-dK-_GhcA:(44OAr3Lc:tKbqsDv#qPort6tBF>.qg+9Fl`ck_=]7Uf<M;(:<9rI^F,y@G$/li+,|`
AT!Um$IGtr9ss`b.]`I"o|c;(f@7WRV?Av;c!N`Ia,9YZ&_*Wkj;?WT2)P`uD?@ct1o|075q7z!"ME3q:i-4G-v?vcwuHEAscdp=DqG|YdXJPrJ0=,*{jC.!3,>-Cw@7p0>?30vT,rukhdXiN0@fK_bhv*.d)ixTPpqP!.*;(.Y@(oD0PU1{(41DO7N>t*eBg8]Ek$TQ_NS~PN0H.OW^/fZPvRT,UcM$4:$/
[hzHm?&0kh^G1?WQkv]p*5gm8Us[^U
eCA=g
WBG_X;*$q;`
J9`]hl[`K08F</mz,22z]C_59M:FDSuGDvNTv4GtOrv{#M2F:=Hb]Svzm/$/@n?"2<r--gH}
D]1&x45h_^`R!#?+{_S4@g%m-w<8LSk5,`^iM:mgrU_m=D^xSx#=Gf&L(6}b,q(@PsWpve1r91D40tgn=`@^dg=Z?uO`E(K.fgk(cs_.i
#r`oPH]w3-kB~2.ETt=-KU5Qq8nf.6.T96YAla#W5@|nu6-VvjWwVyEkDm=j7>FwFm$toe+xV9_w.T[",[2ViXLljl^Tu15=9=RacSE^H=UrF@R?knwCgTD1R#*bE
1;ujY]80XPil=,vQyb.6My);Kj}aZTa![g+u-^^:?5+?;1oUmP,I<&5Kv`pr&]Tm^?5Py<%<+<&[2kaq9`_J
SZMF?(%*)~i|?+Xl>~Iw;*gaWqNi(!L:,{xeN&';case"hr":return'!]^5hbTD)?T)ni`"0SU+i+&3FC!KD^YQ#S5VlFPb_B<gdDSC{O5P0"ao+_Xh8ws75TWezXr1k>Ks?-mw

GH|_(H@LSi>^F4|Ri^ErfbABKX#X9"ur.l|M{_Yj%:GX%7TvSm[7`?1w>3TJXfLc:x#oY6M>eL|gMLO_hcp9aFBDgKD:KK3.)y;XIZd<>#F]0Xw+mAjZ]!Igmm1f5.@Tes__.:0w*O)(s(uEx>-G[d%P&k@0L"USJ
&2P(hO;h?kYMghk[|cq&U[@9?HpNu/hokK|CgOiizWeE/iZYZ/j0jZj5XX)j}tLlT+<t_#CEC@)sMm*Pj+=PFH#Z6p76.KDVMD_Mm_4I|QzbW*WW-+xwyG)3s0Oj~Tii9$DC9`LC<h3nG70t+?3VeO)+4XgQ|/!3jZ:GCuvqiVKP;,z
@nU#AeyL7Jf29:fP{Bj)$5L>X!SSzFEN{P5LD^o-"PPu:Nn/78q"
jvm;^ZJ.CVnL5+&%,l4!oZ"itki0jwEvkONDIkvWQPC37v;$TAlF
$+h()i"m`<RSEBqd*r^vY@?[aOQTXVN)cdnBk53VbYDt.Hc!L)(W]Cx68CK>=sR^bP7_[4g5+spcp480-=ogWL_6a),Mab3gJ
1B#4z_qrAOabXZ2jlwCu5?rwRx<fCw)q<,]QUMpamEMAK_M<$lih8.*8%3
3ji:#MiRqFG2#G`5;DwEW
ZJL"kTf0QB=t
ofMZKv~Fl*`%[RTi%,W3Jd
QD7as|:T7NrSZL0N@DNF@M,-U1g}!<)MwI2#3337i/FBbd<,tf&k>b;f>0Ee<-vLG4_:b&kCMvL"l""P8?@Gn{]:+,Z7!
[iL32:`F;qVzKFp#$F?h_g9WFh[ZF<8S$u6&65Wi8(reAO!D"pyuaZRt+*a:OlZkc~3um*^,1/awo(C8fhC(=V!G%txwKw1/bikSuU5:>*cK&VWgt&VA]b:F>g3t@ttVHuZtY/7*ZQFd?:%c`7Qa,Xx&"XT(!EL^HO&+uZh
/x#@ui1/.G^&?_b;t6#B;x0`1kJ"g`&*33
QtY8Gn{Zmq>*;
-ZiH
cwO%Q-T0lcVCW!wAJ0RPiW$F#[@Lk#9{.Gy.w79`O
u>2/BaqS,msCNt<(bz97K<r~xsM4Po9Cz(9!DhhfAs"K0?axdedGK4!6@ZQIdPn*5v<QCBqI%QL%t~bK8v1VAu/fmhv#]i.6Jbq5!;jr(^z(rAcKxN`F?&:tv.,LA.!|Vqn}p{5OE<PRrTDOjY9!^R/Z4n^U!pHV3go^W+H$EokdS2M|BFAo%^Gs]}9O>667"OROKM0h[W:~cQ;=Zc)Rt-Ve7oQkJmJmJ+Kiysp]sIiz`7:T8:At"Z(WD82KgriiWHXMnx1dWk&N-YR(<4)~%U:$D1^wL]e/,i,w8aKER0W~@VThk
R"=A07!1"]
K]:/jcSOOuEfZ$Ec{g,@Hy>Me0V^6Rz^b_DaH(JUVKDh1"qq3dH&G(<4
g1uWCg"cm6cARLSa)C^Guh_X"|7D5pH*i4#&N5`~7pc5R6+#e@WHRL5pRyu|BcH9e`Q$gr3uAhCqfXsk[L#rHeMWC9CQ&m,U!MN5tjja6^kaC$^;JxZA[:hjfCkjF(d8rMQbf#0+U%y!`h@2mMBp?HKEq}!5B[+j&
"-L)_u7?HU^K#%Kq8WuWSnlxAYA-+uU[Ng6;%G7X2
*}Gy#0rU;7PftW%E#lY]xk.u(Q#:D=kNUuOsakk=6q7dW7mr//[R30"CR:&3b5B5Q/G~]pg}32ouUHgilH]bh1h&6^YD,O`A<zGs1=H#Ly8)a`hX?dUKZ,-YpTmy+#8ok]Y4@{^8ES7rdo[f#i*LvR9RMI4#1wK:kub`*35esOJd%A,9b/7QWIhf*Wc[H?tF*m(@S!:R?EK1XXw"!+CyU~Kh1u-]/jHRTtx)[gJ!6mNRK^:]/Mf-3T]ygA9"Sn
ueAFJsa
Y>I#/@*pN2-I>>84BH?!0(iR,=L8Ah87Br6b>MuV2&ORjD]"v=wori`;KP@XgaD@{r[m.]{n3adTBHg68YRr8o.b7Jq"q="5JVDm^Wh)]Z9^|R?NVEO833iNNG,T7(}:=H*TzV*kP?Cj+0UJxZ27%5c(OWS%iv~PJH~vT8}(.kVC{s`0tqjG&]ibaK7lf1_nZAXV|rUO/x#eStq?;J!E@aW;9Yi?zcxes6QADS:jiQXIotx%IFrfdoc)~26l=?$J=b=-,G?W"+zEjGop+t3iYObWB/2"B8Dsv%V[9Y5^I)g4UB~sR1;o:*_>|k^/19N_yKpY?lq#GC]-vjf8j7[#tcvsia5CS)TVR/6
bW6hpb4_CR#COd|i[qkOZIT8zJ
`8";dWvf_x8$J<`YW.%<Vt&=o$uM*N:lS7<`j{;&*~?3Cwl9
5Vc6m>&-zeA4
OJgg#[:H$ZZ"2NfQ2IXK.UM,eA-u@2vl^%6e>W<icA^%c#1
J6CSHLd;aI[]W<qoZX=c:qOKAe5d^yBfC+)|i2JgC$J1!:IFJ/IqbQT/5P1yipUo!o>Xt8aM/bM}UkiHD)rj)7,<XNK"rp;VKj^-#2&t.u)7w{0La8F#(Y:;h>@Yl%Zf]Z3d)M6y)PPvE_dBTtXg/@8[mp/ITb6ocC
-U;B<[ul$0m1Qo:@ZrE6CI.N[?F)DjH
TM8V2f.`-:NK:NuoG0N^_TuFg.lY4rt1@4ZD[gZec76o{>xN|gq89jB2np
XyFxM2##K_9ape"mRF4xE}yi)49v`x-V%Vqsevm/Wh88=5Lh8nSq[ix}%RDvn--R6g
g+RYIrme{@R*/Q)v](#1xUiFr%8GHJ*T;sHPyYCYoJE<,tn(GWVk
K/U}:(yMmMvS`wrz5d.H?ZCpCh5P7#HN70(zVWgN>sA=D9sCGAlM,oPqoYs|!c,8-8Ru=_ox&-""';case"it":return')]^5haLZ;#>)ns?">pX8e-jAo:o8%"R/4^Yb`,r9M[-t9w15JxwAoA^YQtMCz,.LK;2//3#-qn_l{!6kH
]Ej0,d7dGZJw$P+G`o)GQX:2r@R$i?TO=!jOrB$2qQ5AOlVca-XS`n4.R8H:,,u$y:qVvUP6_jHNl`j#GvRYq!ZjQ6EyxSpu$c[d7d8Sf*uvU5i-~EY>Vxjuq,xE)]hh]A|=zZ6J?y[)k%-&F[|jbYv;g4?.!0HE4k&O2?TJ;0hpETRvoiJMtr
@^lu8p@^>viC.gq_n(.v2{Q,M;c`Z@595bBX$9/&
C/@cH^aIC6BW,PGo|!>,7[~e3#x>#.Y3[[~<CK}!XVU/%g$b00w7m=9yYDk]j;[][]/@
Eq8#<{kmv@XxL/N3)~bq+M:4.W1F?x[)J`QDL/7nQmy7gLqp=9F{om1Qd?<-W$*{d6ZfiDy]t:sO<>uf#Q9j5I0>hBZyBYD~vb^al(`4e5Xf5RE?xD1k
,kvq*qy>
18w(l|Z#Ywn+
TPk71+uWoHA(f61WC2UIq@utR<v@z@9Tqg4
Es]n-$rR"5Xo)=X<G3%;hc8aG90FJ^KCS[jNYa$K|opWwN2J(-F-d$6DLSqfv%"(P+x$q]
#?k^H]Q+]|MwPP:%>_b`8.ijSC(/eztHp<&7RF8z[`it-MM_tSy{Abv9&Yss6f<GG^NH2;KI>6gI(?kOPu"yZ.g@K~:/AR<,QMY&?hA1UK$6f6WrW`V"R6.[FWmdi1^!jUWkqCX8+7K)o/MkiS:rX~R,F=xFRm3FnSx+keT&gW8wS?G4TVI`%e46>lT3R@l;[WhIAE@G5e_[BTA&#sLH@+nG%U:&T51LahGr1KBgSox],Cw)*Bdn7N9b6Se3+zEDv7")cQMEk(RODwy|k"(LeAjRtQrLaJ@:MoM2:6R@mb-$$erPkg8Up"2CbRhP=ejc$[6lD00]/j3U/[POU""@oymq$CGX).l2Mrp9SHM8/).9x%!Ye%&zZV^I?fn}!QrbLL_%dy#14##3V~wr=
RN`]=W+*d7"%juZ8OQN$M%1%y0
K
VR]53$7re#u2k!%!3a(q(l2"r:bO9b#7-0KD]U{ijZ%af#m).v2W@o[lkjC1m]G<qJARPS"-#LTxE1c(/-N
@+C@eYM9M81ovR>Wt`z$QUHU<5)u9RN08T<#5j*E=e6#zRQ#
W2.S)Hwu;;izF4GmG/S_9K2VVfak&Fsb9T10Vx[m!c!H4w8KmvE4t`ObqMZT[jRRu3+An$r1#)AuIY"c-;ECj?`{,PS[ewEbT1kCE?;]E>c#x/;L`aio`E9[*{63({QENs_aB@N-%(Q#N9wOikI3g
mmKY`em$,M
j02&8w:B<KA)~Ck[}Ra^@,9iWc!Tngg`PDUjYrKL3-00rtgY<MekgmI
D65?Ex0??qrZj-|JyK`wQ#U6rd-_K;w^9egqf>QfJ/A;i^7hL_Z<FE_%i2P*q/|Yc#RHwnoS

X2}76l,0{(>>xH@o#Uv]]qKa4Z@-yy8O84BNqat?m#`4DIOx5IiuiKNFIuRqAXz=@;pq9bXv%?FX]
vl^7TD!+SnNB=L;`}-d/%h[P2.w6ZCyse(4?crQ
"ZLc/2DAHp|O#VYqpkCF78;gi;<"i1E:zbAd+`DaV6
.).XN1$#`*(gP"#Cd.@Qc;t^?);l:9u]Rp*wV`?7)raRb;_9X7rM`cFe++y~e#>Rseuab3aTmdoq5E*J62hd(c[cnGb+gOb7B|;5>E?}[:8{80jq[u"ZfThyX!gvcvtSk2VinjiHq9<%R@8wEYo|j~&2D_pB7U_APri/kNpx3G;#17(B^UvmyX?Zh7L#>&k+0t=+_yL+#+pPn!JDtuC#y&>3)Kn)1@kIx{Za)TmUtMJ!=^5*C;m:0_&B==$g_MPv@h*y2tytd5PAFk0t[qPDX:u6&}lyhnTP0xB!(_@dXver`5-atA;Qrlmwqi=^qHhQ[[e#X+^TSr@6#/K[DkX6()J{T@9QI<.*6TX,j#q
u[*kgr@_g-<k[((1DRy<*bqUR.n)FSvL<PAyDf5cUQ93
Q%T"jbobA@$OpUmef^pRqBhL(y6m=?PKO8l$A(<4IS@=f
&@w>:gC-SQ/30V-2R;ovG.g)MBc$8v>j,$EQgVDv-Iz:_IK#[qa#3-NQztz$2(cAeo/d[t|][1w=-kV4RWNS[`-2zLk@rIW6O,7;yK4t*W"er7~NQ7XY{U4rhFeD([-y6lA/lkP`"GDM%ah^e2{f2x:UI?|6?2vPyC9sR:5Q"+Ak/13
y`wVOF3s
`2W~nbe.+x:m]"0aA1WIwUCr;KWD>Q.T$2tze)VK^&]i@gWq[dwxhp3gt_TJ(1=X"2wIqyj5C~I2e]l8ftJCS8u,gVGPczvhXVyF=;y`M:j`*9C5/?
y7n(G+H^N.$v&_DtPA4
:8zsg.9@iU}3Z.2G?dBC*
rJ([|R*#0bc$y#Cy.^rbgfZ
L:9/JrbB;:T8#^W46]8<h2uYC*z8xc}6^';case"lv":return'+s`@ibOZ+:%4oid"J_N)$y?/s$Iey($pD7g9,k?0-975FGu>,y!7],F*%BSDnfsBSSQQ<ry=#IIIT[F^[b?#_A"j8+vL<utiNeMw,(U5&`ENenAl%t!>mnC?v*=3Z;}aO.s#G3,G|Dtm".kFzGv?.qA@bcTh3V[n:RZ4$&j
B1-^rx5;kUj9j0Rso/WB
snHDTWO>TVqMixSh81"Ju|s3W$p9l*<oy.v5o/176+oJ02VyxW4q5.x_z%<<@-:G7wa%[!7hFDX:i318w(a%f{qahyybkXw4<;x^[|2#U}5B</pbs_$%X;M:O@-%u1hhvR^
97/MoS6CqK;4[qCrnF-Nf8=l,R_(k~><HUhJi6a<2W^0Y1%wGPTJ!=cBb<h#s|K.&Novj62*p_0f`sZ>Mxrk0b.ddRQcs`A]*hWA@QB2Pb@=s+vFbp9Fm@YJl(=~lfBCW?P[9/#%81y@0V[
7F+Li<Y_3">M<GlU"o5Pr<vS^1o!:z.HBd=0;v+_H7BHpsT}/<d.14IrK(3YN?
mFSArx?J`<7Km;ir^gS
QjAH5,n^*VPRx#+li?w1M]aI]3:7{s,(3Xep9n0e$oXYO5;@iQze|4Ks3l+5~6j`_$~5|!MH3m@U2QYdd[u#"5rlQKx_>&DJ|=!/p2vM=7"&i8$.49*GW;k`-P@
qblCJTs/K?nG
A2B2;Cwwf
l!LWIE`]k*h1;hRU3kMkAVD}xH^OCM=E"HGGpc`hI"-!1iH2oScz]BglN)wL2AU`S3(h8XO!]TC7!faV@7hYrdcNVi$3L5b%R}6@R/`PrDH,ot1Y*9bzVH
@e{-oQ`7"y(6;-fr)UZ[s7MW(BN>iI9w
nR"q5?VRB/74Btm:,=iPJuAidfyAXt,c2N$XUR=uhi2AxFj
K`w|t1%:w(L0]!9k+.::_cK9=lZXgD8-M}>@wqe3SC!,Cidp?58T5~Um#Xbxcst40s&&+*`T,?In`C;GxM0@XJ^-L;tsw.mEf!9,!
0&5&M#8AaE1A3uj2d(rY*e8l"t[1F9eq;l]bBC/]>lmcib/F!x@)Ej>Jp&m==hWBTb`@xw^lG"n*<2te^VHZ.o<)=P*,ZzoOy.m[ncm?jrk_O)a`W<[|xuM`B;-rahC
Y}qwS#>Coc8:QlF}9MnP^^y;vwNW+ub|:BhUhW6Vv1)9"[fq2l@t;aYE#rKyS,DJtPh
HZ2yXfj!TbF]?!%6[r(t1v,2Tl8j[e7e%c`>U>f.z&B45x?#(n*pCxujo8
Ifx7,(K*c4|6W)j#=v2$i/HOU:vOA@#()H[UIK`Cw
=ew7sMJ+TMe7-brF@vT$>FqM=6Hn>=g`B@"mc^apuHpEQ.t-Ob6Nmh8GyWOUHsB)w!9J(tBb|LS`UdVV($DQJDuD]E=)bgk^ADc#B$O^GrsZ4Y
7JiAr/_:B>QlR3qZOR4P>)c-Wr1dH><l.To?M83}BhAvjqV57^J^NI0{KIZ"U;!)Nb9)WsbbEYDLO+%7Rk!}!gx>dI_}e4%yx2d7X_^SI4wldL%]r.Kvg&djr&=`d#X]J3y;Q3EwBS4`T*,})>/c8;Am8?ek,r?;%@KM)n8UVV]SD}Q},nxn$INPA$c
Q%AJGpVPrFbBl&9(R>i&S,r6Exw6b2wG"i$Oihcs#1gHlNbA
7saB>Knnw#G(n:qz$?hVW%NB+o;7L$~RME>ISWD6[i3Pc*@2j2~N!.0p1`^7>DoP..
=[S.QJ"Fi&@e=Ue&^/!?-(.))rjNI|X14^Bnks$s>)]Si$F!irU)(C!+;pL6:"MQO?tMLk;ir,*{+_C]p{Ll!*8a%F+&tz]P9Xx|wZ3}D"$qR+]<NqF].;byjOK{ykrdI4#o%B%-.@1p5K!W+GJakNdcvqwywFp-Lx2FvT+6k]^7+~[abo`~.i`%E<6P5(Zb@^7oN%9Lgns#fwDUp-*J<C=b&5=0EbNRIy?;>~sg<J`BW/%H]DXb_TjX]%FH&H=kRHxhk^Ii-uCvX6+!p_wS"i72m1`^WNe#"~`nKf"-"v;PxLT8d18KD;8m4Arz;YbUin;S/rK;+{:*3j/~$9Q":{ZVv;n2xRm*V,-[cuj<Yc]Z1,_2AOc5K
P]EiTZq39p^}H=@_%RxNPjnmYYV+4]Xb@}!8Um)LFX;/6-;"h!`5f`DJJ8RH5.VYw
8Cbk1DeGeIX?2*NdRc
xrG*#-UmhYQF00lr{:W87Fp!P&5qR<S0UG0+Maefr^9tZdKvkN:SL/SOq5IIo=f2U!g:i
7=*K=@zLo@Gm~oDvlxP(ls+T@v2ox$Lw/179Nl&NR:gmPp9aGeEHC0ha9$g>Hf4Q;Jd.0!];v=WW(h`KGeR(n`FL8Bj3:Ul`WI39K8,
{"%XtNb`;hg45p1ellHp)c#9ZYI?P]PjJrtZP1+qq#v+J
Bu0slsJl+x8`RGq8Lu},A`:h]10oW<Xrp`WPVJa:f-t,A")uc,@p
b-$0eqC4IySUSRE/`[sE8D5g_37I?8`M66:3@]#/)6E|P|5}b@:bqI0Gi)Y~
`!/p[BgGvG9:BXE9xg4)sFkR2>::25{F!yw""';case"lt":return'(s`5pbOZ+#A`oid"*iW?PLpZ1`,lF(+*4cJ9,PH;2Gr.FaaVmMClFo&4Z#:gLOhg3M5=KiT[)>#5p+ERRPLW2e!^"sSHKnf3Re#k8+*%_lvxri<Y{`,SJ89qF%yj^;mdI14YgT@?a]He+J=*2?4)qH+k@XFZ^r@;oke-39hfHl&_u:J69HJOpr`l.E
P-s8k`>`+NC;-~TK-fiRgVT-/h7AFld]d
/3it=JnuiRt-5peTL*4icX=tJR-Nh!cxbkTSie%hn~A*.XY<&q/(GSVj[CcF3K!:E>]YZs8"esv^cj*8P[ym4Cv.i6&Lrkk6^9,:10)nIf]Xe6wQ]`3:MyJy##PlK>=)w#H>.2",i?I8:|a1kgMXLu@~jW;4x_@=Q
m-Hf6]2Q9,ijGgm`Po[U7AnT<|M
u;Q2Q3kAU+UlKdrn]^::uKr_)qJzo`RT
!M=lVZ/Q3Ykm?]`@`>;A?pW!N^?I=b#v2Q-DmLe4_075hO|9|6IPK-B$,bbh{:%+h%~tSA@e"IjQ?KY2
E!?)xly?0Mkxvs!$lDUI[D3VdaI@?1j=H3U+W$-^5x>er,kMnW4cVu%e<Kri%YI>JU/$EI>.i{l"oJ^uUyXF)4
xkJ+nr+2)?Il11<$c&Vl"]X1WySf(laYdrY-8pgYCjP]#UE9qjl<S.e77uF;RAmJ#]l-zASl,a>+1_xMmXB=ggM;mVNfX4WnBxeIWRsjC(tqh*!cz?U-ShWXMhyTomYvL*K,jy=^wV;+KMPtWoXtWe?x`xON5U979+c>&w,dB89,FyLG9X/YJ(z^"G
Y~P.Nv35P3No1P^brt%4P7yexHTcU9YSuJL8De+5
#(/mwEJr@3EIV@
8wR"btI.x@?]To>R.(ZXD[0ydKE:_PUC4`,s5I-wY^5wHYa8YM[=wCi;VR>%)!o)l]k-C09=22gLfqixlt0JlBZ8v{o(SK[g-CPBHPn[<7D2hNq$bjo[D/8o:)#.%Apj%+X.#k-&8I<Q6sOvrhqpoI[FM!WQ?K#i)w,xQwn&B~:OPK-!D<)KX#11D<<eRE.9q,$=O7vBi~,k73l*3[fh:ILfoH,_J(le/Iy%!I;n!!K@WVwX;T#cf%L;5f=Y4kO{q""%p]%*!bbNRSFK_cLSpO3{x
Nt2|w+8N
D<9iX1S<JR+]EsWbv=&mG/TUDZ:5E&9T~?]@8,Kt[1N:1_sqQn-p_#xHSpTP7.[;zx,mxD~0XoqTKEOq{MO1%W3lR?qtz>?T:t{U~JzFu/=8yjl9{LeNPl2db4:3AA*0UcLJ=<5#GEI$K;U
vmF?/,yWkll_[?T>2E<1<qo<L?MYcL.<02upIpe3:F#^5<0L#PfbK<@]]T>^"n*Y;n$5ESKW5Gdc8pg]1&3Ym5{d`(X?@j2w>^>G
l_LN+KeLR|289WLgOro|E5fX4b6%VYWFpMO"XC.<&OrDaq8PHHCx3h<nD},oSv(+,G_#q:lS@yv-RMTATtiAO{>xWb?M#VVMp4;<CslsVSUKLLO"N]-~0?d~iPM>>)A`5H4;;iO_D`Gw_kAx`"8|PuMk4>:RX]gHxL_T"goZ@^&H:]=1C6eFaJIz_M*uF4s}CEhS!_s3274^a>WCi<MIJ|,UVQF
b*kQ-fjP/EES
^dn9sqJNT_X_sA,.7%G-J%JE(A@$7d#pVnW@/n6AM&gaqrA3`3V^gxb3]MQf-)1![=vIWtbUq#f1T(U17Ty[EP80bqGw#Hpghv^[ByOW,ka][V/:JaBh1dK){U[g!%
g7,rLo*7kTdLcT8,"EFWB<pD%?vyZWn!Vd@8*+acSUsA2uyZdnI4Z;V|<
u5+#@CI|`>lO*t8[)Ch!sCA2U
B7yoqS#sN[R"5KtDorBE:)#2/E1YdjTjRc!F;IHDfZbL(_Gsqcy-lRe_SulnAw]-rt;!-TF[wZn-uf"00F9>G/#ju}
JunR20hK.^+JJohNUJYBU[[?g>O(;QEt2aNs"P;jx(&tf<aau1vGek|,
A"DTbZt(/SJ(Y-Z]Zv>e&BVFnTRxUZ[.JZ*p5EOso,=A)q@JcmBf%Y``s9x(a!wn"EVEbWF
5S!v^)^9/f6)4
K8gUcc4:a_`Uof,8wNfE2>cE';case"ro":return'!]^;C1=+N#?v}_#"*.R*v7eD"hz:p+>71/7rH7<=}A^irQz.uLYqV*}Y.dAfqwd4GCTrmL5UEX.EWf|f,lFIAVh)DAn@%nn^P5;Wtx)qoEo?./Z:jZp*H`KD6H@h42eMKyU7Lw0Uo#`oh(
fi]t]"r`-_<5l6"NGLv?H3DNw/pf=yI#cm[`E@N!sX)(>>1p)PK[p7;e-xx%Y60Sn8xG6<6nJnK-aH<2mz]NJ</ZxJFuv6X"8Y$DD36on&,TE/S8xYL^MZS?O{NG.ib]wtOk&7/D@5,GCjg)=DJ]
)b+#YSMg@B0bp?[/se]h1U0:&O)GG@yvWc<gZc.,KmQQ{f_
sVcFR_1dX(2/
N!4qP4lI*xttOSMM_Pg[:frC[A=OO_wYGXV}$)D(>Qb_W@3iWMxgK;fY7^%~9,SUd4Q<IjV]RmY)<@+AFECkokQt)3$t
<:|@~F3$~1BcGE%@KgyE0:._&
3BYs|=$1hS0*DTpLSR3s~bScXWY4{5-!8H?HtTUb;y!(Y-@@_4uRSng]cvdP
%72}u:!L!<8&h=7$
St8u
Z?AV]WaKR%U5EmRm7"_:FCP.:x$AU.!dLA7t.IR>j2
TkG]W&%#/x_s}-/ttaI>N:Uj{m&A2i_c0,bl1HqXAM`+*gBD5"^aQ8|"L87!/QYJj.$F^Z_r<Ig$0!_Dj8WZe@
GA4w!Pjd4%2(T41|p,S[rk"5_Alj&v/zc8`:
EKIWJNea?*3T4qBcG1
ktD&wb(j)=;[-Y9RMa_[@Ecvr]B*-A3eo9493uy-2uynD:%5)}@?YM9dC%cdnNR]66:>QrOqxun/>xT>0#+>QFv-$yKtlN![t:X(-@Q`F5>iv8mVC;E%`61_&A`um5
S^mNEcN2
TZ__Q{?{NTbulz,<*{ULJ6aqyx;Z2Ami){EV%rK/!j].tOl7)S"!K*yystNh"+L%*e728
5xL3UPjtuUNKG[iIi>e8R;"qCNPoBc)8+6tT]aHRQE;$u!IFZ_f?kklcdJfo)RGj,3`X_lHoPR0Dm`AF-ZvjoAm5,;AZL&vm9XgJw#wtac0Zp3HjE.8{=7"KSwcw3o;:dG*|_zxFiWa"23u@"p>YT8@jRvV?>A?6b}$[g?9F)a4^7%)>2-.VpA^2jsx(FAT#UHBzK)Ca,1FCZW8]1AV{X|h|D(m1fc(9SwEyi}p+kE*[h}im!De>#<4r58Ast6136&j4B2Pku4Of-Ku,OKdys#z(l}u)SSl>eGU;6Aq[^v#w=]I}^!!9mh5#:@"Q,]c6-XN-EBwdnP+y9<({^9
u#K[`33$,^pN}VV?v9(r|gN%*KI4bdCF4Pd,OsyZ
b4;aTXBYA:+RSwa1`=K3]D!Nx6Mf^$@eZHr`hmfRt7[rl[U&R7fBnQ^|eoh8;-^83-b[.,I%$nG^7<(b[9(#G[LQZv
ZQT>tHn&Bt53L05@*y5?mOde^3l,sN=H~5[19[H%`.dbr>2=xPsI:Q~#T+-,h"UTp<We^iRd]FvT>%Z3}(FqaISpE!eMZ(QAT#+)qNSrOhcNx<NF:<Kg5-JxUSofrZlId+l"BsG=cUwcNu,g&Sc*,w!m^0ZE/1>j&D*oZO}?8SiMqTMehiroTxvnS*ak2t1b`:z6G*x`LipYm(^,jc/@s)7
e$:_A9WkL0J)1Z;IlpQTDNIvKlQQ_RN@M&uLY)%W>GfUWt6V/YMrhSZKPk.nZot)%)QKZvtR<Ei(bP7XQv{7?f-R~_Cd1l~ZSIs6_QU,`f/PW5v*8ue#ZNFWfmF3nW`i/Cqvk%CWkd40X,"e0&bs%M4/j;7^NeG!,s>TA[sN$fK2$U!6y*xTa(Ms101[P%mcb^g=F^MDYDhg*i{6Bs(s2tIY76"A_2}cJ_,,[&K[CS&
fH)UEZH&i
Rj5wJr|gTI-IY`J$
gUu+)S+HlaGPNkB6t1K538TFJzpFS-]S*t/ia@Kz5aeudU.J7Ooo"O(iy.Tm_~HUG)I9VKBd):f*c)R6HM:TV&Br2-
Jf;YBYY4q_K5G`mlulH8fW6:W6E8XN<YYt)+4Up_Cf4_2p)]9(;M|re`e(/53"jApKGeY8|6i6j@Qo`d|2G8/8zVrxO<AA:t>`3s"sAD%N$#aqca/e+S2!NE?OA^`6e+O_7%YO{G
rv;5;VL)l<X0#zI-0U[9uM6_d
U#qmq(DZ=|)ctrNW*18~3?7pV&GQ/xZztuJ(*
<WC0U!-~qfZ)CB9FHKQ%rr4z,~e("hmg/#E(52S06$-w_UF
e47"r>sEH125_oGbM^q4
0bYO~4cNC?rnk`<ymJmct@B?,:lcNPVOqddpqPu*q/PPH-x5B-at3i4GVQ^"%Qi.%SxIP9(
Gmo
(teqPE]Esr9Do
01zGMiu0u,eoUnj<Y]5DooGJaS|+YOofG8gmy4lkc832jKm/>iAbdjRK`qYg}
YV3CaMIL.59
fQ+GF>Yc
YHDxf*L-323;Sk#UcL@*&WF}K4*;jiNQ)KxHllNp9ufS<?G@yp$!q|+HC?eo)|U0"K;%2h`D,L:z8#N((>?.<Ssm88VI>2P64b#.&Nye+1p)<Mpju"R<*!+lt8G<ISj!8:
e?X#Igv>Ge,QaQ:E"1{X*aF.Pe<DgX7O~P^?G/{[bsw!2Rm=p*Kuj4{G?aP*%j(5]4B+f_T"_WwGq_5l?Vi;tT<
3o,UwKg;Gx@B@b&+yTWnA%DAOn&c9r
J"x<Vp&L0sLo3Ax)@?*f.mOe_[Y^mb#MRIfQ12-i!]rl`!N7J
ZIvf3R:2PV6;D!uU[^c[rq<f@~sts=N82ve]$zKre(4k#><+9JWU10">XJ
AV)R>G~u(ka>%V~:h>jQ]Sn.r^X<
Ss<Vhxpko?K|YsPE%!iR?~7yVVR[n?do.ekcN*@.z#<8sO9
;X#;oz%RL@]Y]l`J!(D)D[0>?"?Ux`N(daMAP0+D

q5C,Y-VC+mCQ0~j)_:kWL*@
x}r$a(;-pgHS#e';case"hu":return'-Zu@)aMDYAzvhvte/*.,`#j$,Z`O_UoZ+(-h`p.^jyqqsw*[z=;x_84gAlxW/-."N$n#G=]xf7~.UKw_NK7
q]_5F&rV[B0]0XSM=JxqrA#e&4}uqS?]ZK0c<%WE8#0y24c)JCIf0twx^71mqvK=18x(~y@[U+6dq^?:_
av|a2-E)VmY%?**[D/mi/sW<C`GG.Io9$[IAB9}s0@bLo>R_05;=w7.KmF-gt8GTKCrkJ,e<yGoxbxcyAW&SM*$,Ayq&zcJ42s^Chbo7ULe*(N[jw7pfq=!NAao5HPdxIIWsv-c"3#^]`:XZ3dUW1m8?}&:ZD@+voD6YuaXB#yaojc~l!^-NBqZ.O1<xET!G|eD0jPTd_sln|0%27J(n@y0@<9F-x&-@7bb^{I!qWu`p$&mf%pky{tcHRq%v?e[B]0yTEtG)K-#2<t;%aB)Dfb->4T74WeqazB#r&s}8X@3y!d1GE/vpv*46-vo&`WZ:~mxo^%wm-uT-I;OOMQymUkngFW
1<tAUP_NZCmTBJ94c3SrrU^a+=wp+oF)d-C@:V?q4m(Mlu6}-RO&a^]"jQ$8rT9p@#E!a;MEa1e3E>.-Su^9f7l&*cS22jl^J#v,I.wATG,YC`"Qi/h[
0Lri}dQFt:%vzuK4"*hpc+=/]12((,T`})]O-<CsRXf5o?S[j@VkK9a-qH1[=?_FBPc201:q{WVXnq[-#f&@
gQsn&-e^otaH)%[m2L%<tY$9
$N:3A-&GBmtu)A@/=+DA/xbwuMnVK-O7aF(NxFj"bUQA4:2jxe%)jtDfQFE7[OAdE+
4;M(/p,e()
)N~01ysUpAZNOus+m9HVo5PSoY=v-aGgBGfRi)FJkOZ.f`]u?Y1iJVY8?>v*w!
i."$JSJ.ne6a/@byPd?k.?XY
7.+s3?T`yw`Qpq)t^TSWkFJQ&6Q@}vESX54B]Z|;!8-[[J_&#wG,?_WH(gPb:/~vXfc,7jHdsv2,z7Bg^>1]8?OW[iZ,mkI)Uj/.=#[[4!mXPbH*b4+#E4BdTciJ9nhU3he]K#<T:_-gP4`Q0_D4|VC`DB&K}+w4=V!U1<xvD;=DZ,6A/04GM]fNMA,0r+y"M>=doi"e+%y.@::s#pSW:ES+5T:1wldm.xK>C!b;WT--zv:CBc1n"U/PEg$i_3<kW#:6BS4kdco[CyA@N/?qc1!Fs^fS$#~s)BBa&Dn2!IZnqw,TUw6ZmrwlZ-d5F1T&f3wFvX$X3G"p&"M[Z)V:p@WX#s!hY&z5m7RQL`,rMNKoJk3EfO%q-f>8mI^:[AiKg=rH,04AusxYX0()OZ]#FoT.b+A;hbk9Jjs9@<my"$15BC94Q
RT^"zr/GMtI@I?6C
,iR$D!yrGx$2tVo"sD8$UmB_`qLPCasVm[XuWRnvM%$3@=2CtY)UY]P82l)L/z-WwWT5Cbr`i:MB*OjT]e7RwesvZ{]kC((8<ct;122*"-`CF3]+:&C4"*XUJ`ucuul(/br;[k#8/cTJEuV8@2>^?$H/y6Ye=[%;U{h$PNeYCElje}WnOz%1KsZN(2HSa2q2gY:j3^_:"Q$!#^@&G<FNofeuVOlQ?2)(GR[swA"cSEN*=D5pMmRtUfK]3z>3F7G=Qs={.x2[UI(.eMvQmTs"Qs_b:N]I,m({jr,"ST39BjX6-AjbOPwck5dzn-.qA2=_Lb73y&%+Dm786?P{/]`3-&X%y%PZ+x2)i/%ENx*|1KV~(trdC:T<K:MREm29.j9wS+/R-jSY.@
9*^Xd0SpxW,BxP*&j
gmHwR%7UnkGR9=#$,e;%<Y,(98S5+KD=v=*Wm*FZYc>KkN%T"K<(hEe[)f>ud5HRtQ90p?-_mDa?/9.MKe4u*n+BZ/zVkj0<7"di?8m0.:24-/vR2w?JfVTlM-Dhir;hPyfgJ[NpC]OMNdH[zm6o+4Mv;X%WeDIO&WiLSucO;@R7WsT
IdjA-h;>18;]U@eF9l#%2.[y!:;M9diHM0MGD0$8E&-LA8+bk%f2THl]Y@;mE6K!6giRd98XG%5YiGba!%H%0?;J):pMhOv!gyw&U62".tC0pOr>"Gv%9-8%W?fsqtbJcbmLJ%.k#E.8D2Z[Sl:?Bd/T(^%&~ES:FI0kEbOl*42nF$
O?Mu
!$0J6ow9OI`[iLVP93"(s/ZWE^gQ6M|,t4Z-9$AG)Rzk~YPs!E
]v>Ik;Prd{![!lW1>
XK$rD&mh6P0et?/.vVF#`q/1p9@s(n[>EZ;$"DQV"0L^Oxu+uxSfX2$ZYJY>H[pTgx8Jv@@|2$1vp<W&B9lhKx=C[IV0-~@WC1[mbG#)6Oxq=/,JW~Ix6G)TUd%
(k0j#iQ-Bd)o2o4"X}V?K
Jw:R;)"*;,T)hM&BX%tSxIFz?A_<8E0d98+"+1u1gp!nW,nf9QUas1,)>+(*J3y?Bb8prHolIWMa_[8!k--um;Q_5L(sP*?GOkR!f~qO$1=5Q]wl@C[#hIxYj`/<lApO,C3"4Mu9M<j!g13gy2@:Iju~>KWP2
"E8OpAA$V{3}*^;WEK+/H3u_gk2h!4TPT}70,tU?p6^H&1r$."Z=?IG3d@,xtr<im:[a)zCWS8L{C=@glYk{-`U(D%y93[ZiQTQxP/r^V*`JybX/qolgeT13rRq2)R.I?h_}f:GhQ^;eVFTXk">rxJ&`4U%Yi;h01%",0,cZPTys8(189*?D_QukRo#&Q#Qp.Nv)D"hI5|I}J}2GO^9T0o.|ahA9VU6bj1J*bXSA8SLg:;I{A[tWqhL%6wmlpjpvFq/rU?7n2fJhIor1Lw23s~V}WHG]y:+JhLFjuu3rt%5BL,C**ztdi0V?.|A$8KBQs:U/)7)~`/Wntv0$d9#nNaY@5UE;Uyh
a`g:Ge9(t^`r5=,|imfmx/dTteHqJ"?FW(^P:l3zeX6KY0P8m:rk6Fv8K0T{_DN{O#0RVJi{>89enY1;i1r!FL>k`zp0jVTp2CN6L|*HLq%w"NS!]MhAfO#C!tNXa#-
Ye9s@0_XCPj|0|Rno!c/]xI`Joy2*
8&kPoz+
1q=$99/}WnP/<USREa0>NKim
`*Q"mt.KBW<d(';case"nl":return'%Z}6SbOZ+$sBjrK$8).1s3G<+TJlu+a?M%=#]
x,2Y_Z$k2-c>8kEmh5h*IoKy3pey;JLiwNF;0G*3lvkXW7filkS,^3~"
9EDe[Xn~1GD
X,*Lg3>cI<-}pWgslw3h4R1Af7_6;P>(NE4sHOA*=2BrA6,$?`r&h(Q3FHw+K~.1=Np;^&w;II#6A=yEIN7{l.Y_)je,uNW*=f6hMDW`G;!Pff.QxS387is^.$XeivEH?2MhdiUUQ*"6F0qL
quO]g1_w7@}P7RDL)Y<<!vmRsnDL`*PNBab7;1VsmVi]#?]s~>/Q:mkX<),Q>E^J`kvZO=>Q[W6)ZvLJ?3Uy|1Omz$J#dQ^KHUb4Yn0
c?{`WZ+ye*6_UOlf15mdym&"o8w)#&q1c]^jwdfGFTo<B2aTP&Y%5%S/FCwra;G&e=%v@)[[?gw5J?0V?a&>da%l|?zU7
Ci=/+.@<D(->G`>5hyNe?tm6l@{G|bq>zLAebg?`aDQ+{fb9.v,?D_TJE2)@"*]l!N-QvmK]<.UR`$FcaKDfN@V9l(O0]f4o~,<?
WeCR<Lej[)v0<h;|,B*fOjQrGDc43yvLb]x3EGB:N]PA^=>ZECC#f5t@)ru9kPNl^$o8)r)
#~*.a`I^7xyMfuf93.dGo#NIy`P5M>UuYH5t$x,1hQj7cFlo(0(jQk-"(lc3Hl_rV/f]H|?ON#B)jO+T#PO
$Skrb6_%_i:U_s
DO-w%%K@nkWhB:VK7a?vGg5WXxf_&W)s+`&TX?lR&J9;EET,H5pmd>,R`UG`lL?E>:Sw2hcr/uVM74G)xZg6^FF`+.L`F;BofYvgg:Mn$-vP6HmEfF/(B*q^bHvr/nyUmm4TK0$euoS><D2ofp0!"(^qPhPc)9#?Z/_vd=)@x^0o%^4e-J_WF9P52b>iMg""n44c)&cLYR1G#X+O>Aad)E3=CK{>fWtDW824xl/i?r#Sxx`tzR8o7u-qP_w1/oeO==v]epg22sxP&)nP<&Tf0%TPh,c)CY-kK(d7CH&p%t+r;RmvGO
k7>=cw0]]Gw%(c6zyt0.)h(g%DI|UVgm8#XuHPfi$CCzve$Y"vie"@#hksfBU1<*?Gd[
+7.k*y9jf.x-=/42oO(bbf,QE2zqle;5Co:):%/Np0b=8tyEyS2dzw$#:nk`>@+4#ufa9%heA`Ph+)=*@:G4D;].-SI?BOOeSAH=dp*=NhP+#>4)E>Y_>j.9A1{ac*]>k@8<<rV$vO:Mqkrkc.cD~rk"94jiiYmBx1;Y3DQg>$o-=UQC*Q;BPIgi5*o8P/3Lrp=v;)%hoc5%J[hR::E;hYOifO|12.?U(2zJ[1r0t>hE|:7nM2zhiN,8FhQnl+Al&/-_x$X+H6"U#l:"}G.ebG1IO^-u1
3kgllJSoK-~A{:R_$k5W>;?F9=!w8ZtolGuwFJwMl=F<eA2lEF>9T_`.~n,Q!id
9ah,ATYB8H9lXI5
7i~>+AdH}>NUns+WQbP@E9LRHSSF<_49F$}^{rNaXaQ_,HC9<Mh[w3Oa1!^`c;e)u93Z`lN>fNsbxUz
c7_;L2"?b+m]-.
jdTT"lfdk!#r+Gvxv>.jFh(gT#MQj]gG89"DLBdKRKQa]_(F0sw8x0_K2!HZJJWkX(
N=&xmXO=HdER)S8m?eap?:L4X_{]}!lDU;Gk:I3uyLPO(:!;
@yYaP61Jh=m=XKde]XF>?"t)AfI~a)1g3,nnD@DySvAh`DeAgk&k^;9MplBY_e
A0^xQ6Q@VxXvvPjo,IR`5)5-1Sy*M(]m6M!U
VCd;E~=??tbx=Qr=cqQ!BSc[nQKv/BF(VWELoa=sa<1qfz9]yFflK|&E3,XbrJ.u4ZJ+Yt?yJxNbEc/itHx_#JE}$esW<U_bD3I1E?xTr3
_E-iWGw"#jPh]tU,&W^FJU{xpo%r1x0$qFQ
3=k#72-@PugQQ^KN-xN8uJD:mo$+Vo:w.075{wA
WMUp4Sn2R/2Zl[Z?u8?60l0ppS4]f%d%yQzoUuKeeB^!|xx(oGb!K<n=0L^Sf`VB8tA;kurW%XN);)tq@$6JWXW?3pj*%8JO0L&^ku<U4SY*-pU(??TWoQw?fN~!WuT^(X!LSYL0#<?K?kTg`w17+Z30$h]+(d<rJ*_3S](!M
5XIm)3mf)GphLI7c;C_7iw0i&:ncdW!8(J4eP
DVGTbt
sD0P?%tRJ
+D[Zbg,an;#qH{D!;/X@gH=m4R&IG[fDZB7|BF<&B
;np#`|<q#jy*wi;NJcVaoQ;Hc}p?a{Co<_3VA/)Tqo6|3YQKk5Y;hppL_Yh"_vEh@fRFcn;I8ywe^`I*+>``T#]ZK/M-Z5/&+ff:7/-58qA+%4[Sh#(DR%*7#Vt,><&+J#Kj+=Rq>Fvz$eSo.,DL
}!>6Tt+Z]E86We_a4jJdX3eBHTM(ZtaN!SmfU.6Km[Eh%]Y</iwwmHVZ"KV$/mj"a1~pUh.nf,BmB%3Rhf$fM1cP;#x0rw`$l#fNMXB<(';case"no":return')Z}@qbP+>:u^J?19/_RCX8{9mr&4;[H#W)("qc7f2F;>^8TYndEXlCcN7MT?/xc6+x(PcX3;7/6lHT$"B^{!uQKz&tS5=sncMoz&B,D^vE5we*gPW[Jl2`?Jc"!U/,/mU"H]It38<u57TD8INLAG8Lm5|bJ/wucs`jh>aD#gV/&3*KALoi1Z?tYUO^^-p9!M65/B]#wE)1%yfd!lyQn"<HA;}33q~f,5z<DR1oE0|*()St|LG&L.;b!0*
m:hYO#49h+3LRDPC35H$WIUt34pmqn<eD"1N=/{6Ak?P4qP^0>YeC:"4;`{VzduX&JI1!jQs0paFnP>(2Md$$pJ5REY!cY*NDM&!)4^4I?ic06#3MrKp<M)Y)2~cx=B[E[it}wuPMg]Vlz%X^V]gkN2fT)
*<Al#RlzLDe9^efEimu]wt4)U#mS?D*o64;o-+w4>;,t])(~]v77:wG@7q1r@|I5R+28%?!WvF,iE[9475_+5Gt[RxU$b{uVKg2j^o_O(9&]]9S_$jI%iDgLM!"d,[Rqr,U@P~X"
p+b?1NH=CqmGR.$%$:`<G!]qc3<8XC#M85)+&]3nj5e;&Rmf|$,7uWDYnv@<;RrnnGL+BEJ#,qQ,G?TdlGF3JjJg2)J_<+/0l%?Z(`c^;?t,Xr
rk#+4.N<vqH{avU1o7lHpH<j!wJeK+CP$&`$f
*D+8*$r-@8*YQ(T54>.^LACd82g?v&vCpUdV&;Aix@Bhe$/6iAwx+7>7L!5@^>,/mR`(0K(YFnnVhM.1d"<H.BF^NnY:xOrJK9/wWw"/e.WoThb4p*j;2VI!Bjp>YW2n9loW.%&~.d9kL_;mZDp}hzTqy1GMNbW)#>BcHGNZahhYtM^"vyf4>bNccyM|`hXon*Rk$=@]?_1Wqk=nEx*Ihi1eBt>7:cso#9e.hYB@9X$9TbD[")*xfE#CM&Vlnb_d<d<Zx0_(cT"NVI.9)6e,q#Npkn?&MAMt/qqPL66W46g4tTLKF3&"m^&i`-iJi,iyNf&Tw
qKF1R^BDk
&BE{(YWhPQL&,zAAjOe/A>yZ?pjwKE8>X4^62Wld%A_[9&w<`ZM3ymWUUTQU:J7
"#<j^wU9x<D.CwKol5GUi(4r4UpCYyL~B~%q1O,w.SDh<]6En?%ePa@|*,GwqP#Cl@;]Ps2uL&w9I&MEKU2RZC?Fv}QzoL_dOUwS%wQ/<Ut@8-B:;UR;@b@Zbb>PvDWy`7@sWaV2o{Ji<83pq-.c#?D]Lg8A)LZZ0W?d-4t1xZ(F/ul&$K@J;x09cpQ}T+H
ftH>K7Ovo[
F5,ks1BGD$cy:]~]<q</4Dznug1gyXRwXBTvc;=3"*]lSfNfG?;hq0;TFP**tCb,)Gpo?OM,yUfOBhBdIP(<a9Y$;fk[ybX]9*{_>v_2}gCw-w/0]t~Hx"WYKP.(>d2lh/TS@rattraUvw/Ol02;[L*2G8>te02=U+V8$GWY7PKA$
~6x<7;ca]n2i)#A.Ig=VMJBH,UhR:-$NLU%PM$JdUNDSC9J1^FJ9/0%fu/k!sZynsA%vj3HDrt-PU)?avK[=5_?wj(PiKW0-$$k1%1PD[UJvq4j98<T`VWr3~t_-m/SesbV.OnFpg0mh;UnesF`!2:r:{yIY%#%H?PXDrNc/iwDQgk;PzesawecljmAlI_3Oz-uQoxDQ6OIa&c;sC/s-lq-y7hZDi2O*.R,b+cqU.rWf4Rygc
J&nre^&g^pe.g93)@E/rC0nGbu,jEU$QT6!=;R+[t,J0|otO>t7VOFc0;@}?q]]oub0-sy|hJ`sTBTRWAr%y(D>PS.L3V`,Y=Fw=5Cp2Z<Z8{ivYIV
T;.Ta*4,[x;6^q@N:h!7:._uinqqt2"oUBfu9CYPw3g@WwG<=uR
(y&CQ@%1>r!`3maLvF/7ix1>L?:zVXZ.A)o1FEUQr?Y?XZ5xFtACORtcJ!TT0.RhO~ffy>=b!iA!6_@I@"#Hi-2oA!/
(EESJ;P;+/U9sh/uPK5*q?&}1A_$xU[z<-ABmRLVroj4B^pA8x4C_UrQfcv<DMh"e$DMo_=%v(!0>()NJCRXaC@kuB=AFS1
E;Q+@q9K2)@iBS(Whaso5BmgT*Z&WPiDwl5n+N_#vkZ)+sNunEUWB[fbXx_%%]"dI|$*rPP}:X
t"<Dz;Trwtkkr,7-Dg6K/%5*[rxrs($=S
8(U0H#5/&Ua7>3Ngx4Lip1PVcdz:bPxVlN.C[x3-M+Z.gGLI9_.OuWS=`uwIk7d[ieZE9M+WH;;Z
Vw0%;5uS4dD[viTUbcOyQ7eWT"ASKyKGSS<8iR];U**{=QM6_E#Yx9a_G8D:$0MUvPW^hr(0e2%"HUfI*j_#!tqrNmDuKwu?9eY.NuxdToXe7v131qWiLL5w0KSdSLf1]-G*6CVu1)YJCYkh>?^Sn!wP,:VFp/<!uP!%W2M+F/Y;HBSWSJeDZ^6!gvYx5!@K7?D2Q-IHOcQG5S=
A8xf<Zd=Jp%3*-[35xZ7THy4<v5.,>#ec)Y_=pT"FP`.Sj3W>/htZg8kM{1/';case"uz":return'.s`09h"+>$#5$lB
&_k2&,dfX6@lj9kFkiy8u+~eLwrMm:tc>>3jL=E61Iw#l$2b~;[mHYK""8YxCMr[.&C.o>`w:ua6Fd`6Lk2#s,+!c9=b]#KX)+7+fN94|m-fblqj;EykCx*F~>>D")|;fTo0%t~OU=qk!&/w55e!CIC0a+bT-8#.s1qa/X"JJBY9>4RTS?k6W2,d&n(:O8x:i[TtWyxR|WQ&c
dv!q)1=ID
1L*c)Z!YHR,GKewX]6VrNVk/C.%?:;)()l*COq@Db4O>|l(UlEW3xeG[jqSb?w?Dmy)FrET[
mjsQZ1?jpsr.ntt/u[u9]&$FjYKDpC&<dLkDF_v$xZ+^o_Ao!.1ZYT67eAp*2C>CX)=}I4mt-pnDc:$`Z[[%QB`5p/;cPwnL$SU29CVQLRAmiCe|i`Q(;&iu/&M4Zg
i"iN3i0K_DFNcsWW$$bJBW#Y87e&zQCUUUK+P!*vNty/oiF>(%^`v%Wk<%A#XT%aq.SumYOm<
k&4tQbjV.3K*Up>Ed:UlE,86}8fnPC~&)&ctknek|^Pc]95/kb@7MQ{K
j!yHnU9dQ_EX?]e*`<1wWDL6&c[xndos0hgG0z5XY+;l#hIr&1j>nIM?)=9MtG<0eb1=/O6r/NXo+
FK%:HNv&cy<FZB3on*LGOG51[.Xe+~<&SBE`8ulK+ayA%c/h8!dqI{uoW@Z*jl1?<00k,W_?F~s}jnp_)]09.x-%b@>4Ki2+>co?HqLE@~t&^**%9jD)#0WD&tGBx}(E<3]z:q[HvUjl2Uwqkx=StsI|.M,Awf#XO*,u+!7m-}`2N16R>&^DRfvsZ3wDA>&$R"!71y9m%Ts{ivyyY+r0V1)vwVe:2oHwqO>.D*.`nCS">[-k7Z`m8+Et[T,IH
8)FbOdx5(M(,xa:iX.E~hCh]>v*w#7"}i,K04SDfc=OUxe"1a!aAO;-a3=fep%m!3P_;P:kn+*5o0lYXKjQt%fxW[<tjiU`jVw%K#7UBAy/&XCx$$|O;g8eb?WulD,ovf`4=:y-RyIQ9v2!m+PS{.XH,kn!/$oWwI]6GG-6H+W9GEl)r`
(vvclZZWdiH&5`>+@Al%qgqEB{%M]#Y.5uKe6|xP%</iBvp]9{d2TDC.&<*.vF`Q"SC%3*87ta--*lsq><?%:w!H8F]=HXCO$yqzGv+5I}85tefYko]O*Kr.3z2TZ$82
g!IV1d).r1Q&M,*,X:5(P]QkIKL-ngz=uL^3{d4&:6F@o.=6b3!<?2(?^NnK:tDh!Va@x[#E8/D=afU3Dlq>]0eGKKz4Z2^c)KTj4L|H-D82#@eOSqS49^X[Xf/ACq`wt2LPJ,`!N[@Z^"vPi__a9l(=:P0_
]
D0Lot&@r_vp9+@SwFlFIc;Z#TYpWEi"F1k"e3}Z@as4.3W(ZP}&2#Kb"v5c!]-?Y5i]jNK+9UAib_ngZae$YK+yb2O$wkXct^F"aMqkdvXykRkJz(dOQ;hg,IUaRX#%^
yj%><,fbG9(:*IfnHsU1L.#Vp.+jyNTQs9lg2HNLc0-mUIE<vcERA#3U94A"u"|R]FX#x8&S?/lL*-(!L!T/YC1`SGX%OyR)+8H5|kI5$oMoH`T_V!([wixcm?j1CgUd+!v3yr"7GX]3a?
Y1#f&(<Dtw4e%6qY"=CXK3*LIhez*f$HXVk`TQk>/tM`FhUF!8Llt=?eC@Gc;BN]ct.t(,i]Sde0Y`koD!Awt*@hBKj#*Ft@!.G0?{)(4]q*LTe0Qxl5=7cbsMEd"Y%/lk
X%Ht+2P]8[Nyy(LV3`a3]]&<Qb^G,-/.>D0)yqM?ZpjORFB
cfAD{gL=hu(T)Q+1%Ajy4_87/6[]__N3h:AG(szPican96"u1JI_Yyk`TY~$*4ZuNb|sVGq9!=}y>g<F+R[r<72DQ-,ZaB/p@d%$Cbf6h"66,JrT/)5;4XRWzdAf|8C`+i
+Z9H^[&?^F7jvVi*S(Jf58uKn)3zlV12)v!>MjCU)|$I+QMsm,^2e/mUllLDM=cG-A!w!Wef]$1U[1b=T3-tMhGS`:Datu[0U,IP%oNu(ku|3ot3n%r.sCjUZiq|K7Mc4olM*O]<6tKJ:Tq.aG_9o!@uKci&oyI:x(QWUM:2T@"4_9=5%1ru`T/08w*9,1R.aY
@"Crh+bJ+msqL!"B`U=1^h~)`O}$8*9qR&gtu1xkI3/BcK&Jl>?jY^N(jEvX,[c6uO1*n/_thq|]IE![2r)!9<&X|lJ`]r@2pA,yt@&i-mE;_djLNtb?C6$VP?k,_)@_H<y26ef5]g)ejLWVrFMw#bhN,%)g
mMqf4`jnaZ@i51]9]V0hN&S63o!Yg"X1qkpFgk3+#V5[,UY5DsOik9aAf%O~/to:d+sMsB/Iwh;p?S(??e1R[Gq9n-fTn45nxZY3Vg1R;3?HhT9u6:hXw&o(r~e9Uj1wwh1ILB]q;aow"KfWv3fGm|fwra/F?z"-%%;*H)L(D2kU_7-Z66Eh5_G
G%hE(Rxd""';case"pl":return'.]^5q6Kn!UUS(f{"YbR2WmOYw`5<@.NZ]YAHZ$Tr#.=26Qw?/#U0~k=dW^o$7!qb;n,l/C<r4MUI>jD;++V%BG{`q,yw@_phB>55}-2B}R/GIl[>,wLm@NPH7GUJw+R77hp.RI"xpnH>QtSv|^4=r9U=A]OBsHs^9BHYA`}F{8*?Uxqt%u*GO70mICM-o<%7Q1l&c`ciGpKR!r>hp[FRG
w"EXao}@JkLyf`yy5;N,[=sC:M?9>0*&G+LHC_kH_NH5$hSs26ze"/&#O5vL|N|fk.5c>ni+2V)c:B&S3jTS&vF86F;?;YBm@.!wCFcaNcUsw
BW(ST-Ho@t-(&KrvLdi1E=r?&@&qJZupfc;rM`h2|R97q3vuRHZb`XyP@qN3y%q#lb?twNhv~eQ1xBEuuuPl{B83$MAhaj1k#
hHq?)Xg5k%X%0u-5I2t<6N%[Uc
3v0G4">k?y2Mv%=<g]:H,H%^cHASc[Q}eu+`E9Z:#%aw1a?0RakopoNE%FRz@@?97R8`^4AV&s$^X
q;QuqG0{Q!rw
Z(Qgn&.P-i9,Urkt~_XK$r+X~YD1qLV]J_!gi*7Zh3ahJ#XF~q!v1j#b$oMX"%wsUx,,^qKaV-$ky$CZb:*FMI9vsFEKtfbvW>lDb]&;VT$&,E@eMe}r9<zLb%#FNP;UV:E-5,~!?$!>:+>Kisa)T^YLGI6>}a!uGDmU"X4O/-Fs3]!s}s-k;g4MdpqTpHuWXX5sRtx],@M0V
dyK=MtR"xpZ=W=k
T;|TQmi=g<X.uK-ZhdmC-&mw+Pgs}@6B=IogJ.7kv>zZzrv=][rcqUzt7QORiS9`zoR%wUX)X-pf3x1H~f"($HGP0d
2Mn4tM#CJ{[_#_50!BfF_8cWry%?b7FU6*%F5-
Jn[Q;x?armTs1xU&Srt>lC}Pa40QHo3T<yGZ0<Gq|!:V<3:J}vlId*8_BA)Mn3.Nd#0lk3-_oabtZs2A68q+g0|9w/vyQ,]exsX]%pJ
G*WXjr4?%*@Nn<Z,suWN%]%C2#hP0)!v<)=7&J(jR0PTH6!:$x$e?][a=]M`!prgBQTvC+$.gZv)P
s+Qp]Gpn1[z,1UY=N=lQOpa)|GgNE;z=
]1RgJ(6,qdB%/horf6k4^jJ-#MKoeVxQ`<V@xYt#6-2^[CuLozb6(Y,ioV,WRq^RBzMHkd$Jg2/4-meb`$*2fPmrL[F;ozP#_DY{v&1nwu7jJRekTWw>#9-O)XT~.|d:HTy#w%]oft2^/2
{RfH|:7pQ@~>6nP<%:
z(iAU^)90LR=!Xi0SJpib`j+x`hol^:=
;!eq?tPU48Y$<2%R6vhQqJkt6U!"]A|u{-e65idhbZ@LUA^m(-&i*Gv@Rw~g_4Re6R}F5t4>IC9
ZQ9Beu2ag$:DqZEvOI0g&Ijoz/*5!f9.0n/.&bM
~;`ZXo1[.thAu(T6cBznbt**;o<!b`>SWQ/rb#EB!_"_p0)s@-KioS6bpkuul0ldhuG"bpTr3MvYsU9N762)}<K08DQ/6>v,Wd$@4?-o[P.-++Q:~i
.P!Ci7ioV`-ui5XVC=:.+oPY*T3q>idw?i#:Kr:@u=cC+BJ(Xu=_C3KYg<;a:I_[=&SZfhwB-S*Fxq=
?72?%n,;eCf3wSlK"yT"cnJoQI$(._;.!4DI#6C&r8dlGK!vPgQpK~wW<>%+PI#4XtS1B_c0*d(@
|hd;7Xkx="py?cWf3NuPs8_(9v0SCS`8Jr#h,GLmgus34?:`(^&OK]kQfs4*o8mHXyT[~6i7!&j(apE0%Jj-5ir!a:Ri6gdPQi__(eE$N+E6V)/pti{-
t+.jf3Hd2]YS:Wrb-6RKS<q#IwG^DH`!ZuSrPLikf6ba`$!xLg#z[ohV&yqbR]K7;]e&j?ANs8Y8[)!=i6eL#YveK2n;K"e+XZM<9o;X=FiO//12*#2@V]sSeV@&Zsv[#9[sf+)P3ZU"c;=gCDVScHfL07=}ZHWLiS#Kd?<*w+2ExtPb&lT.mPX;p]6_"g3*2/QAs)Rw77DUQ6ubN#issXtbKL[/H6JHp*1`)5Pv,Glob!%hVDou0@I&]XWIu]@`iOuz$76C_EUXjU:Nou?_E#C0$KgyOD5SJm@K"%666M!314v)=D8:q1>%!>3LYkXM&B&
g>XvBvt}=-.`7r
y+Fyr7nR6=<r#a!0-7gs.u~Jp5*(1izL6)Xa79F5Jn>U:;*(_4/=LPUqL=X1O8Kf,=!a5Rl
y,{e3B+AIi;fewg0~r&2MBf0AKOD<7%"7
%GenRecFL]5_1p6cPqv_#U+Uh]rLQiFcX#V,]W1=#V%2ol<HG>HLE=)>?L>xpC
k#m[oh2gcki|3$f7_=lI+XPl
N)6</vC<vYm:&ngwW6i<91k]M3%$0Hm4"66[$uP!F78
~AZMf8FmtpoJc`n81%y:zx!-&;5v<1qZf[HApAFZEeO@"?,g+EA2vc-6,#ET1c!du<%BPP^A?`"P;vssLYQ(=Rys">&@|eP.$_?%WbvdxV~$8(Ck[0qAN!Uw
KzUj,|jsYQS,Vs=/sEMZ@$nQiY%
xOw721l,Wq2
@oR^5Ay4@1?g`#c-vdCW@yC!>]%{mQJKpDR<B-hPl,p+&=yT>{E(qnoOQCv|<WG=j//|R=6"m&H1Y9e4,)5=!Ux:<#wMd(yDF~pzTr`)NdSG8cKAT^ERQ8HW?[A9QZnkjkA+2b`ZXB#)#K><Ya$$"3.+e?c%Z^$WCTY*$i>Xq$pDcK).V6sQbto,X&fvX6rpu*Ru[WKvXjc78[5{KB-.5>R")weDbIy?iHT~ccJ@C!PdpmR5Z4OfQPCi7QQ10wP#y$`^Es`Q*PhCb$^a-.J-a0%fjn#37%T5B"sFB
#P=/5]mQj@f{06RVgFm1#WN1jICvMP5}Gi<.Au*}[=N#v3D[YXQTPrxeZbU,2UJ-?^iaeX;j_zEU[q)y*#!zb~<L^G6/,r.lq
>qR/$k`J(G3[_+GG*{yj70H-EnLS?GSCkra%VDvQ^vaTv=I=n<[[eE"Tcq"zZJ@*pA.E_{uHl0-2(r9jJYOY*_<y0i1MY4dS_><+`b5OxuK0d5m;&y2K(Bse.:;>.BWBi}hbl
64,_pqyODQ4OQ`nG=-^IBn"+$H4lc,L#v+0Q1Zt[N&';case"pt":return'+]^@j5ID9(mEZr"%V$HL0&)E|J`TCF2,D.{Urb<rMAEA2(pG[ECj&w=OHdJf5O)SuLd!4:Dz&:)em3ZClT
Ck(2Slb7?SpX
)*]5/nwU}9Hy~Gqqv6{cuW[)%bd[="S)IZjL,L@4G[?LoD,XGC+*oZ]dZ5`1^(/R#o+KeJ:I0jqebgID7YLw;CTe"qlbZy0[,7@8Ztasx=?
+9HAB&4OWh)7
"LP;v@JXF{?%kS+w5;&}5~d!x#K9xn&{T_8|bUJ*khV;Qx7(:imn[Ks^CA6wy?ll*igk/9-z7X@Bl13*l!etHjt+OxAmoJ9(>X.Ks=d?y:<VD:Puc8;3a>pF7;>^O1XVN|I?O-(Ve&6NBpWQI%!$+Dy~7RIHod:,c7uoiF=aL"V:f(</kQvd)iaZaXe%<NeEw(E[f0$GyZ;-4x&%P5VA_7(qBS+o#H
:yMwwT:n?f+N(.;A5fxhm)aCJ*}])QdV",kp
%FUdr0yzKPgCi1RQqAMh8{t
Ea/T9C2t"i[%sa7,X3mQ62olZMky"pE9*XQAX!&Nr|HF]175N$LMqZUs?Wm`OLa8,HQ=>*5Ui7E&1q=co79IT2&?GF6>oEQQn|
WPKZgo5StI|`6d@s)29QC[0Ykfgsu)P.!ni09T&1G/kY4n1L_5rBOTo5MR`FEaa,y@p%G6;)4rqI8o?M(9@lT=E_X#o*A:7HKAx)s#&8nexK*1u$#u?vyV(5WCN^h-p/`!ib6==+,)9T~u^
i*]dm!+a/LG8`lN<_kA+>;jxKvBFI4+ryHcbtR@8J]5!hB:wHf=o?-y>^[G53O-C(PcdeR
"r,8gVmew,o0e<E+p2mju{t04Fsm@|vd)=3.x-JG@r)f9nZ@G`1.d%RmcRU$Le]7,n<*b5qUbE>k[Aio_M*n"am3JWR?7`K(1#CG[Bp,BjPttZPF^bxrBNZzQnsIHs#5yQb/>j[aT99tt(@yh.*Tp75,=]FcB;,-4)Y|NN0C9
RT-r
UL>-XgNXn0
C&-9V"+{S;SZtURVD`"&qu3S9wJ&<F&9Mia-#5xQ1?&Gi>g%vh>0u@+0Zfo.4@P=J9yFm}U@)F,E-VlMYfMY3:Z)yc-QQ02O?1kA)gGs2*,xox_9KKTA?t("rpfppT*|X
k8&0vZ/*0l926YePT@HZK2@N"OhD"rf|S.6gxanYCv?+jRSBrFo4!4roflq[3psLg|]X;EUH>LF%Qaoa:Ox5,ZCX&](hhaGgTUOR3Fprovjxum]!tSU*romTk"0iha/W:A4aO"3XK-On803c#M@4Kr!zuqwIP[whKgo3>Pv-9E*b4xnv0T
-04bfWz/7x_kKKc5a3VG?1~bIs5o-4xCkT:j><KH:w@Q1%jU++-G8l=pX%^PbS5<y&,<AVUC=QHa5Topbihads7)0Obyl4a=Z`$n#C9;P*Xmgs6&V-P(g_WCv7=j#PnMG"Bti=FF^Rf=4*=k0XE,).Y=;U*RwgWm8oSw+gl<_QaCZeh4Yeo^yB$u9I0YW12`}FSI)<+q`$wI=Mc)N7<upHB2+t/dloj#N?ktD+}=P62<TDFMASJxykT/.^P%$:8`!jsJB@:h9?^HFZP4O"UUKgE/ziw%c)y0fSWvhmN4kl3GHA8d>)1)s
D;$C,Y^XCQXgiws_G
NJJ]mvXKTq>gCHeF5.4mGPx=&J6nx7&22Q3j?+r;+a#y4Q3##wnPoc<&2u`X^[?q%M;r+&}n?=oZ;4Cf,,/Z,CqDTY#%&QfRw4XfFsfyR^8(%H"1:
CZv$xV>-tRwq=.YX?1Jc_.pOutsRgAY@W[WmnEjpq%d,CBf/e2*`J:Tg*w"a7i6vEr:fA!{bh/$(NBm"}`zL"#!w+q>sfP#tO09uf<Zqr9F^f_F[r5D<T)&Wj51Vw#:uF&vW[]6+Jp
4gYUG&4}!@X[Vu@tQRiQ)+h#Z!S?8]-]gf,WxqHOda]lez0f#LhJ0Xp5]&8,bz?F!0X@ilV5<305FW869nQgGL_AcKdPNAI<lxU=r/F99*M8Dlr{2H^c+&,Oi^iELTh1vW=oYSqg$*!G(CyH$xVTXP%^]^46^3z(l#lh9rS=R:GM_bq#Zj;5,dK{y>`C=#&9xPHA,VWn<)HYYCF/?s25B`W"QU/6aU8,;=BN?*?X$7Mqz&1[Dk7.HR9V<B2G]%;F#,*0
=
XdG#)F8KVyCQ$PZfBi?Rj;@B:G[FO!_*~E~KrCrW`Vb$=v2+%(rW/1MjD<*^|kPRGXG.04n/Kk>EoM"9I>K#J#omf.Qf5MNF/V"wv_P-LrvQmWqeJ04Sj,U1L)fHrNxcGZcnP7!?p]"Wki,ca
y6X$~@d&W]z_MMka2,52u+IxKF;KO7)jOyTfaa*wx1Soua"g`-6l}4|*[X*)3V|DJHn%APG_r[}TB/rCYmE0oM0f3^%:-*yf<(Rv0u6KaFzu{hQ3~vH;(GFeL
,s&.Jf/mN<|[.bFC(Gc-#aJaGl
F&U6P1oTGNs"O%tCiLE<;nms<RRJAR?<2hwfjJi1s}+`pqQaavJUHwqAgit-;>.FPqSP9GAb+"5DN{9]q=U`8;D
pv*oe&PiRS2&nFN4Vc1DP|x:yKw+p.w6=0D-Z+)$U|eZnpM#X=289+9Ow<Tr:ipE91%alou`_]ubrWeY<<VO698WxLnXA-6S>L1y[`2EnoJmSUtd:GkR:cZ=swqc>]H3W>1Y,nY@9`+86<;rK|Q1FS+i0IFnu|U
3HZHFdO
g<b08lw-Y<%Fu.,t*t
!0-:t
hmuKOj1(UK;.W?p2a9r?5-uH{Zs@&X>1@k,PphFv=7uMTyg^V';case"pt-br":return'-]^;Bbl.7#@EZlU/I+H%56:YoDydh$,&qc3sR_4m.6Yjqa("pwaMg9N9COY=Tt|2=xvMu;k1$B<LLsjQ_IG6Eb1MmQkv[L4F]y.9X#DhIyy1Bx?Hm=Uhj,eu{4};MuvJ[FA=MogI.aN9?/8rlP4k-_5f+/.Udell@f4Jy!0[
RlQ&BcvZ&!]v@,L3:c^s>UdNVK1ae-eY/C2,!n/=Q{"P+0)nl*R%Pxl#T6`Lp$SURki}o$w@LKt-S|fi/
sCQs52&TLR%wvXn|e(RXkE0NsponQM0eG>(`[Imhy~+7`~eR]TB
u*+xoz.X1
(<oppf&_6Vq}(J`^xHtU
P(,Ar^CEvg
Y*"k@X.G=^:LH#am?~!KM0y-h$9]M_wKYBG$x<tr?16$Fv^L9]Vzf/Jihjfh#!y`hD-3$Ce-<1Y%%kP`BuN&f<e9aJQ8H0qV.$tX8(&3X5(W"nUaa4^Y>3K+8G^#1rakVOu70VAQ%J0expV*b-xm$6*uBTbU3c"?HD"_r2BxVSZ.x;yQ.:*L,uuu+vTkr<m^[NE<g"[GmT*[4R0)Ay`&,=Wt-jV^`
f].n=v?$McU6Y$tv$y%/qr9&"6LK_ED~E3U{K);l4
n6.Xr$ee0(u#*lpU7BU
Dv$6u9B)Ih1qq6-G<^
0YIdSZ%`BEO7F8^=.Rm8=`AqP/vpfu*crC(XDp3`n+kS/(,tiFXqh@p-1Uc/+=`A?QJ9/Ld:jnb$1.!"J@XGM`)jG0_tzh[kd9{nYry1ebs<:,2]5&JWiTKu/a"b<N+<RV$]s">4vd:4YcI^;EpS}q3CYM,]1,0UcF~nwN.BfVqE8-lO.>cUP!N?B^Zx).Dv+LCy:wOX[BSOQF::L$S?>O@Mlh(jX*P<327c;-@IWbQcWRw>

~$POU"_&"pRbD&,wmX<EODwf
(V_GFwXt>8DbN`n&ToV::=y5=XAX[JI{4Th
Q]kE"=S}pVS.$h)Ar"n#%U2GJ<jVo.+uMN68=!+?Y3lG3p5M(LW-A?gzp
"WNOn-Hm[{D!<PqMxKsMf8W>"_c|Ee9s$cx]X<+R@OQ?MeJ}`5b8M4lm]<d|UQy}0cT`2_U*[pla,/]-nS)6N-M?&3,5g7d7o0M_!z+4lVcx!?xcn3Ds%"0`fY02Um<YwcR/LU4UyOkNvpZfPI1Zo):cX:Fn%KPe!_X"I0)rTxHd.tFZQ,a8A#X[4vJqH,GKe`Db]WbpnI9)
l`$o-WPaoRUS`C{eIY:+Pa7A:MwVw
X%0M<m3PF=X_8NC1p7-jq^b5_<~]6E=hY(;1L_]P,[vAd?_?{*4-=FNP4+IGklO,|&^@>@[v#Dwb*bnYojktV>]Y|p>;KBj)iH61_On1j.00Y"{6J>.Z%Rpm=X>]3Wc$Pe`GG
$6M3s"5t8%#eT.#A3CaT/HD+03-/{kn/U-N0fbplh)BCz]HR8V
Dl
:soAh;9G`u}ait=A[O@PASo8b/mUZG>u8[th_)qp$Y<^s1X,e%;9~se$epi5NWisQ8mals,&eF7
;u@3D<U(KTSC7(G_NI%xN&NNtG/QqdVU/g;5b@g:#B4`?j)DXiph*#`)SQ1;sEbYYMb[)qi7*13c/L<c6XonM$Ku!x^8+1Hv)m/HuMpIZ^p6pJ?/KQ3nQ)lKcZNM)lb.%V.jHqh7pT[X,/A<WGZiS=LQ$tg
4b?swqdXM/=ed3Wt[Iq`lR0"KM=n4(EppK{/VP0(cpA+?ftl/=wm[SrmRkCh1u#J)n4?^Jc9EL:)kwE!3sEZu"ZGMPKJU)dd];?nN=eL=LeOo^F:-^fEBF;@nJTl~HgEcD(]@nPeKdg@t8EPFvB,M-FkY2%VXaO4eI5Y/E}p>0jYHxO=vl3k@)>L&1&NBaL67LJ(`ikx34ivyCTniR|+{7UA%C([P!p3A[{]YH=Ump%l1aObIX<(`[P)VUXiUa#w%^:v>$D(=tj%K(,-~QPZoR]/V-6!4Sx,ca]r-7+XfH-:v5y60;ODLW~QtHeF|X6=N<p]=H(w0r/"|B"u@@s>v8M[!yk]85d47Yp57bWcKPy!,9.2@Rjk4[M*{kpOUp$v?j(Vk
Tq3I^wI,6bv?uD)Vc=&E?k|bA2^Y.SBRcu<4:;CkrdLArb+jN
%.&G6]|l%rn1<Tpd4m=WMQ?s[WTTH0.XH?al2CHl]an(mlNU)q?_d7KW~@O@(Ua=rB+;$=xiBheM%U3dbM&raPq;xn@D^`Z"dtb4BXxsv(`Lvnh_Bp%HC9"h^li1WfwI>RS+QC/eZj0":4Dqmqu0g+(3>;7gR#rjkI9`k!V[Z?snnQ<HQ:}v%6|vxX1^-S3(34UPW7m]"*h+y>(NbVc5_+~>/C!LR84?&"{ut!fae9/:4,2SFST9N$wY]mV1-G-i.Z.l!Z*8v=W5jLA&:Ft6#
Uq}l
hikD%^lC]OCK6t`Mp51DM4CWH)jVIQ_g.vP8-&&H*xs3yW5fBKrIm{"zP?4.I@<u?&U-5]L{hocdF@JO%YC#FN+n@n_*Ax<A`?j[?eT-Attgp<We-s`&$Jj8]04HdTcB7A*|lqH0dH0I4xX$d2g6]^r%J0%X4`qOdBJ/9Zv|KlfRXQ3j]]^I9Js+$+vA<nCv/!u
j=tN0
<cq9m3O}l~28p]b9Y|.41@&y/UFHQp[e?-VqcHh(UV.aG`vN"-tFliS=Z#HS.j#=/Gtc-r<]XC?j3T<Z:]H!-tG-5,V?6Jht_2+v:k[.
t:qUt0~)4xo?3G}r0HYw82RWYi@9eoq2#eu13w3
L[N;E;_,Gpt^AG;2oj74]F`&mUC#hyGN&';case"sk":return'*]^5)6OpM@W!(ie"]@dK=rL2yR!$Y9=NSsD"sj%%6SuQqT!PQ.jHp6LLCIH2b-2#%4z9GU%7}:)egmar<UuPaC2t<NK*7H(LSi>cmuJ9;5Qt#wz=pvpW6K5-9Jj3ms[ZaV%#B5o!hpaGah%+~O|k#ES)vfnD?1%PEHg!h7wa@[[FkqM]#l_?dQbtsN.e`Nx.viY/JFa"?,VZm@6-~Zf_NGlOF/UI`t(k
OK
MbC/?fvRH;qKV3H8XR%baz!l/w1N@PScuuq/{T71*`K4!NMDXK4$xI!s`IKW94r1d-BlI3pT/W5I/Bh(l:tuLiO+0Yca9Ia4@lLnv;#Vam"j
nf6D2F:FI!^@raM)c=(x;"5U),k~`c#uAC
ED+hi^MV4`KC<F[=(FcE#`xe7s5G8.3_0["z$B.y6MeWFp9fN(CrFeVC>r;9vXZvI,~H^3R&>uF@An{K61pZ%I?n~23tFb,[oav?7SM%o[:)W4Y7Mg%x,D!.T`dTCo
["]~iYgpA-/&9/bFb&+#.-S|sMF^%Zg#jKJ+Bkjsw#I`5%o_,,sc=-E/,hbh3b_7^.e6q@<SO&mo
y>`$.?{kT>clJRDOnSYo)K?7S*YjLTnBf8<]gFk.)T0oPN=c:qc+pcfW4DiEECbA84-&LPxr$C

#YPCpvZbUQpMC/>=e_2VWD]]9J+;>O[Ajk`I<-V9eYU!$ex.AwKFNp"HnyDB"XIIR4+j2U
cw26bgH%s.9.ucMlGQkM?qNXU1Hv<Z[xZU6pek?4FxK;;tEby!AUKd-|ePA+lH+zAI*hbt`yWuGTEY]yZ<!jn)5bUAG!:$.T0W3^SVwcRfgQ`7o4?:]G%RRfl,XMJv5J)wIkpXQ:7{(wx.F073r#=)5FUH+e<t
TqS`Fc_!!15D/
*WKW9Z5;}mh<Ul~
zRY&WdE<NFJwyTqKON^:DUe`0-jtX/G5d
V3laxj=.~9u9Q.m`O
|K
"lja1C>*6a(3AH;cQZ_b)f@+YxZn8<jGs]W!A8v(5pXHGD_K:W]>16?J(i3w^+jbfE$l*/pM=VqmAY[yI{NeTbqM8H3J4&Mx@.QUXh>_N~DO&A.z:shw@&C*M}0gc>%U=)4BqUL3Xp+a>$3l+ni-hqbWb:[LGg@<t,Vx.tD(;uE~QdA=H%QJrUZ"+a=^VeTWz(D]?8N%FEtqxxLbEOS9A!RI5C"HQ&->Jbf7Za!:[=:AM.neB"c0;"dA<xH{&8*SYue,sm$gvMZsNh8qDl@F)`>V`-hpS*U.7xo(y_Kr[Jdog"t]5?Z"CkY;I#p2b[`poN$:m!2+990CJ<e#/gv;K<Fb_S[$j41W>~]ch8oz[58yEI<_9h9"S<ILO}&QMmX&+y]9Cuo!]TCk=0D{kE?l"])x??)Co(@,nh2;J=5950tfE<q9.q@~]@JC_6,7]5C8**!k.K<@OAq"6d>MF5G10WOV":2jw#R_,6=eezN=0{IaUf<K*L2gI`)LNfNo-EwWOrCkOwQH(FIE^JjX!T1P,4fpSM]Zf@*#wAQpU5:=^z5j(|QLcz3NSkd<8:/+<uHm+CwIhbw{Nnj06kALtiL5G*c9#IgzH_(n]Ct/MN"PME[V(2sK*K1fGQ8.@#CxZe
I$Jami8Krn.)p3P+j1+eLoE18;+LHa@6wd*FCy$SxGX;$fvXuHhCl<@OD!^fN.{.?>6MS-[g@$13@0Y_FqxC+oZ$M]CeW37%,sl))_52c64OJYa-9vKNJpC*?A6N,-}0^N?;&Q.Uk8D1~xa!ol[xzuI/yqwlMw&8XoRuQvPwAa5eRWh2B%&=zSkeX;e0KJfd15eUt5~e~"&ZgdjG-doDeW(wAdM$Gf4TrN=$DtZDvqvfP_UEzEt(WEmB)&gB`3t1!x2e~tFF#NMF11`RiFFJcgV`MDCZcHAA$-6qc<Xn{.Y6duy<#"j`m1TEg^^-8j7B%M>6(Aav}bbx8A;Er+2nf(ewL3aS>If^>&VxyKm<~%`[.G1;&46Z]Wj
XoRWzJ9^ZU|X&MMT<Ma4,>lRV9feTw/dwHg-}[8TD`t?yNJTIeK."i-[FqV_?Fqf?AI<3Mv.(ErLe;!u",_[|_fYuh{3tjnB#F+(-
|2eoJ8Q;q;A`J#fws@Gu-rn[@;sbRBo:J82YnQ$@De]7)#*8cLM6]hc:d8wrnm5LJ!(#J>G:!lRLfq4Bo6=>On}&f*DgKhf:;6zGehBw_>C^6j=lwA$*_)H=S8JosrUnqfBw.n)Iu>bI"sEDzRf+/<~@(j[?a]J[K^SrhX8m$U_t~r`lIp<sVj(W+Pl[@3WdsTKWsUQm`bR+zeln9J:D:5040c<*km0aF6fc!09)uFZHKL/%4K}kIHA/b(d^T#|+Ar0XX*>Fv*,IX${gf?RWUyr&,w?e6_k?0[1X=GX!8jYIX=[!14kNn;h%AU~j&390s**HJq[EC.RSXh7KS:PYvYZqS3_aroA6H]Ka
Qfi"@xg*(0!(.`RuTV?!^-Bgvh<(q~bGPqyWM=RNVijG-22:Q32h</AIyb-6MgcE3N^.<u+sjF%<SOb_"I[I1&!qTB<2@~4-#Xf5#BdoOJ2zCHk?ToL!ceH?mdO1gC)np3j0x!3]3RMw@.5OE_K$a2q0EE3c&e0|e2`(bs!3$vMisIh?,aG|JCNqe@&qP:QKV,t:7!8q.6ee?$HHdoT1%G>u<>?=1R@qH`S~MLW5eHVS[s%C*mPg:HE*V=^.m2;W^}GQ)T!s;+a#f2z)<e/(YjM(
<AAr%9%Lz("$
l
K|bUndndUvmAa[:hf|O`y{ghb<<8oXK/K!^{=s0axu_r0]_E8bACbeT6T.g.8KGg&4*+rcWX&L3/jut]rr924^^d_")=J@W/QR^
]{VFB!RbE{yF[{Z&j%!QA,C@FtdQaE:A1=7[*4z#]uG
9^Jj6l&Bfv-ch/e"#i8OKLtl]5xK2[>>"18$gC>5)^<Lg,m=f:ZL.S*kNgDv+~
h2n]f
{T,#[-wa;eA<WRq*^C<LQnIginUX(u@.e,+P9bGy(do*9-ZcXiY
rj5cYSm5^Xw#D!}0q)Ck@N#%imsJ^NK!Vd!&AwrV[rO6XACCK&ph-7.-_.A!u_T%LQBX|""';case"sl":return'#Zu;:aQD9)QXkE{"mbP-2;v,4^[;98!SU!(6^b?UWn#?S_K58)mcM$J-b"+M#?PDSwESMbs7}lBe.@mXpi&r+(S6m*/n>@aV]n"`1iQvjoEj/s,a4auc
UMmMS?x[MI^M]VbTejKq7<Dq1SXAxqdF6_3t;3K;MB:O?3KvBOH|j$XkY-<md!N^>hYFfFK;!4I~DU^EB~`0rMaPZp1N`46.^dLM4p`Hrqw3Xsy~t.mQNvcXEZsKs>wzo8&~p4H}.-B5n9#?>53{n?,.fzCs/l&,M?kq/?rO/K2?KNuZ[B#6f0rm*06GJZvQVe.)DQw)K/OJx=/Hdc)Lb3v6=~>2Ry%FoJnvr^D!H1[b?^v,@4jt/`0{G3({o;y"A6MKL?gAseyh6(y}0d!%kTxTmXJEk2/KO7O,6^lyWBk5sMlK*PNMK~eLLXG_mT13lW`R/2ZRG9v
]WG=9je^!RK!20xg*s$@hyUzaGu|AcIBiJ]2)yD^muiThVOygLhfdQ&9q3(eweT:e=3y,G
t@evDWhc8X3</vN+>+%`2NhSR%/m#H$Cw9q.ZCAFv-6Nc(hE6y?7bf5Fd7m/-I>?R?_Phl-Y;r[Mc,tl25ft5KxlgO+,`G8204DXs3T"I@=PpicsTi<q-Z/$Gj%]5#5r|P"wZxdqYsb/<5*1&JiYi=""+?H,xb:u/(d^7X(+!%k+]nh3kv!!b)<wLMlcxO:#@we7>Vym#
W#R>HJZ.
F.*@m5,rv<OyTH1mhMt.u1aX995air.<Hw,CE1P%xFbLODUR
}B7-
9[jK"@(.[#Li%8`D&}T4qg:N9,LzrU*uh-L8,n5<syK!LQmg$]A;0sC2prteJ#(dBN4T/ce?Kj3JenyhmF]_;EViGr@%2A=O!*LAaQyc%N6}-?56kJodnP,w*tdeEfvFWd=U[rHy&yH1:J9+NvtmFJ_7<+WsF|Zqt^(Zan3e8.$2G.V%*|OwsE0orF:e+0(PoL/Yf>?76P@Q=$qC`n5X-yg6Nu-v(/I]?Ob-V&?orItiSHUHl,wTU4=1nPQ_*"tdoXM-A#PcY]"$&
q&.gb-ctMaWTmmwwkLVUI$8h5(SLvywFce#ca|nu[;um<*p!o1#S*6,y$Fr[`&<0g_pO0G-S7B=pO=j;`]h.qBO+sSwCdeK0*Gt|TsA|!VBGC#7$L_Lsj+Ak
l/!DMg/Hzhj:AyBn8wv5(e/!)FhE&"x?12`"E2N#zCV-
AH[-$)7
JOuA_lbU>/`[vl/~@@G<QTB"#aO:2XCS;-p<x,&6ElCP!Vyu<kB<<CA}y,1aA4%`e:qdRcq(&.ESG2R+!k*;_8*29hgGm=L}Xs6oAW!1$g3|mRAT&F<sj1:%K@>6"T-%%]KXKDv~&w(=-N7_u``d&=Cp9j5
Hut~#^R@8Y0;jd8?OkENyaC+P5%xO%98#>dBRXtjQ2d+FI5>Bf3KXWW%+(k|:%],eP/}eUS6-Rt?Zwjx1Wjt<?Xv8xA"Ouvx]bcf#h=3NTf15$g<j|W~?qhVWH3CYLLp(*,NoRO;f!OFp1sp`;K;bj.yBMmu#Wyp[ypIC:*H()RF$H`1jho-&-kFBHs~AwZNIRr}r}Pf)bhCLP/_F@]cI#xzkwZ@Y5V|;yt(a,?L!a%;5F`,*U543M7qR#6ErBIF4K1$<96.St9s(e)u"a.Ip]%oc&m$yteV(e<DqW^%gC+jDG-KYoD,^<m!;QU*+G>tVilTLgL@/[8E4swO]gZ&D_@xlN5J%M$(*k-x`;"U33lC*DX`NdO/ltQfHHp"VwFd6GVGujo8YHVA#hK|]+8XdvKB6^3F@r.Dt4uY[!UE
|+,$1D.u
!:q;U{rbQ;,)L5,|I.D1Zn@e,pH(o4
cd.(dkIig?`rzx:n[Ub"/p)B7QSqX>HEV7UqN0h$2$&X*hseqTMH^w32a&1#;Q1M(E3a0Nb>A
Gom;embs5#gN<_e(/;cMiC|x|5&)zi5N92!T[1DbPv~,d#$wnvqJa;&7x5xK7S/u*r
bdN!HIqnX`pLF//_pGY0_tfT4P1LGZ[w+G_7baW(W(UJPZ(:-(b#@_24dtpnCg8`nf%o##E9,&p)==$@,[R@2%,6Q}oaR.mPO55GJyaM`c]#JfQeR)$9Q`V,7t:fU](KG8S]2~]xdTt.iEB%qJ/W3._L&
+yj!:E&tAuZCD}tt9i%vp`5/xQ+.GYH#6h^hxQ#le%PlMzUrmV0p^PV6;=S=gUQ%aYs@G(m5:W>7!p2;`=bv4M"Mt|jIg#lsy4AE7*1}O/$NhoRMZ(kV^`4!K39^A17]Z2oRq%]1f0f_*DVt)q0llWdI&cUP#GN!a[itD"sT8,a|B;d4FoC,RQDOZgQkh8UH_+8Si"b}@pp>Y[75A/%*f(fYYaV0Y+YM0i+-bt9"W6(FL>3aGuchrmCT^Ng^b98Qr[)=vuvo*qC+;`umxZ?miz4cbN`larUxa<^=0yiB<2Weo.>!$TN+qb00uRH0iy&OF?1O3F>TnZ`a=IOl.>
H2~20I!ZY=HdI
TR7e@j.hP$mkQ4U_^(5<7ap3KkpA(;V03YHWgeiyb;im<_JGvI)8M1SQUIcpxY$t%
OLVKxr{KHx[
OIKlahY4F9?nt_JnCr7:$9QdZ1FMBVo3J"oTU9YrQCP3*8f67?#+Z9^fpPg4c!hnmKa!"t[KT+aiQ$Q0_NgPxQt9;Rtp)Qz(>A?v!,fS)f2#oQ|(FqI:HWB>6v.oyUxdI%=NJo.4q8Xf?*G<pD8%XF&"R%w9>MQSK]Qihs!KL_#E~1P"GJj98OH_GML6LM0Dl`RbA4f`%]am$A]G>8T*6LFN%NU=zYiY]g^e}iQ>Gd3,a^wfas_;:(O:e*>N]5=!&"*h8M>t/1;59sk<ioojpVDw_pU7djdvH1hQ`=-9b3<YMWliIghkBJCsdjX';case"fi":return'(X/;;6LA@C_S?eKOAkwYV-55<#CI"]irZ?|=4&/ECL%=wNx0^N2gv3
V}2^k&kg<~aqv0T1!@/l+XQc?{q^lxqjsdc
kd5Z]hbYh>1kXp4Rw")*[%@
%1o5%=1@61`VaVttMlrnnqD.77$/7|JgwL(mh`_b45;lPl98mcK&]`J7:Hqx89<#QCysS;,6V:g~1:ALZ%hqS<vSZ#SSp;)zMjFio(y|`oA^Poi1ft(aSCH
L?N[%lRk/kV|HM:qpn*gf|a!U;F/nYqG+fX$l4TIc*u|Jf2[3*^9XtDe;ZX,DBB$vvt^xe`x4d19+{g$5#B66Th<sn,s`t]+Mx[5q<&wLiXv*,q?2b`_lX<Ob#ma&ak_ZwS%%2a0tGJ!v6Wu"#?4gyI-2YEe+iZ@v_7@9ac=D<HLXDqgT".g$KHoadJa@>gndC=OFzYl
)$`w~veFJe(PDfBTV$"g4bGT9
t8z7L:IOn5i7E[3QfMZJk&;[]ve[3dgf}htj_
JP^w`C8lb`+c7$*5LDJ8uXDG%um6rN(XJs>){&K(ia136_ZhvS{Ks8$:Kc6G|dMnA;Yx-0|]sYct?2nJ+L#3&
1gzc6fOh5%PqBC{?MLEf}tM!aV0n<=|LtXvqLAfSP4k`Te7l92ky:nBOIHtb.,!WR:bLET,aE6[2sR*aM%=fpDd#Gnv*6T1CK(3DoG03(k#$H/<RY"=@z==6r&uDF;5034aFc:5qsAQ(QI!nqO.2H<o&kVb)dN`&u*b4.4?jtn%/5&<@$.nGL3Y%[S~g-LCuvH
[$`CSzyE4NnM,=:C2i%*<@qO&+SV7[d1h8%vMIeBI+,Ss;i%$u?Ve<,0K$K)BF@[`CA?z!M2ES^YN
a!S.yyWmHa^n[?e>2%G2d
SD#ThCF8Nd^-`KM"V_a_2fKkRk"`iQf};Ni
JG5nG@2h%[r$yt^Z
RB}QK5g&}Dc0GLm575(">cX7i9j.>yN.G,xNpE"ybS*-bZlNL@5I#jl8m.VdCYEIae)/w.B=qedNdw+=)E4o{Ke9$x!4R$5LW:xdA4
X>E9Wb:sY2qm9Ru<vE9vBx+udK`v,%_bB~>*s[_x[(i"xpy:HD`s9j7;HdXXbf8VI*&n$5-fN,#l9NTK!X&:R+qs5@CP#iJJ9a&`33rQE>4{n^&8)h$y-,&gMI2yPWhJ?^
+NI3/[02*)vEs<E^=XVZ{Y*Ce.r13N+ymDa3f"OiPP^"aL0ix`^d+fpSX
oqMVN?yE,&antn{ftWDudU021"-HvgW50p,jwK7v+unZAV=C$,|mH$
rRQ03Q7h(H-kyesvB;eQ;}tA9*,{)))!MSvx.Em)>-m_8S5!hj5jC8)(){.n<3/99x=rP03;=Tb{>)#m/EdEoyW(/WF]hJi28_SohHfmlWM>N2"1v702v;N=/9F5`?k8GV1?V0Hr
RoprK--R8g:?W[a>ztu22`TO!;KgS)O-_w$yd9`f[=O0eJ;QAlS
)+9wQ
rfBlCPzl<YsH|(}y^ecwr.X-F_%P
w+d=Z;W^`=G57Egm$oY#h>Okg0K.n:/kT:1`_`w{#q^C%qR~a,Jz9QlSe#:TJ}*Hif)kp=finF6<8T2A#kS(M"1tw`1]4$MU>AwX"%`dC,r^N@V>NJ]:6z0!fk>6C}2ACDa9hfZ1#lJMsuc-O}[sti`-LRx[iK(%C)
CW}4(%[r:t8&.m&ZBJ.bp!Qab;YUp59wA){dPa,vQwIRfp=a@^yp-W$]y:6&rbTlZ>+!T"sW+5&hxE!GVa2FwxQ)(;&P&A111py[<sVnXa*MBe$`EXSi"bb*KI6u3L=ZA1v8|ab`_4yXk=eq*rESl;XaxL?+B&MUy8SFpB4[X-87B?bL}C2DE`MFq0~p.17:1)Q]_#|Z
(&p].K0&^"PV=5=YNU!wKRHs)?Bp2Y6C@O0xlG%@=`uv4*e%;E7C)}q(0S-GuzS
WGsoAtwd%a*}kh(FPE6=@qq?3-F"n~>Iu%o<M&aR?O8@`WDoi|yoN,$Eo9.Ii(`[uYTFl=7PhtdQ.q"8-)5N-W9cut7rIH9YFL"]Eon.%u1H$>v`8c;At@ZA0@dzOZs?U^GAZJRD0q&c96_QqPn0x!5u<WkIIb5_MRfwYSA4IzHKr8y_k}kiO0-A<eH}j*G"^W(xo@Z{<$f([5rb&%*N+3C0`BC{/mG#9NY#J
c,7u&:s/Y_pip5[}Nj*];owrNf0S<}+#tm4VshdO^G`HNO-9[zf~J[Ku@J1:ry0$CXgO;MfCqwF)=)W+:[;"PlDl7$s~^C-86{REONJ/k*b=x-H^h$qGp$$2VHx3H<WU%p<3&CO8S]*W(YrIb6Chc-9|ubP{>*S!5XH^VAUB%[cn9$rKq1FWk#"]U6k(w!:%liGPrj<fmzxvS`
KT*@}0h(ZG_H{hx`WeBOOv28ZIM&!_T@/EGBq,AQ@;)@.X
Jb:1#n#[ivw02f-~ii
/2Y,hfmp$a19hESx}PKG;%9s@6pcJSN%B)Nm<6sy<9%&ghWCNcL#u`~[$Pi2!NR).^M4:Ahuxf;hZWJDhY
?rF1D$.l`[Xq9=v|a0W_D<XYec"$$L_a=(,k@*3d,q]/&$vf0~d2xCF8*.]ly2pBli_!Gcy=@gh_K
89
no=sj
F
vv;d9P<hV<690CV/S"xH*Z<?,CE8uF89-%/x$O2Y}U>bDZM)y75]@TWI>q"A
x{c"uL8T_]21[N$"M=?XanM[Nk?L<bfu-v-m&^B#TJT2R-&A)7DWM4y22^BV"pDJ]2b6N|T%Q]xEDr)3&pPRmdi@[Z0`!&Veici#U#nW
E3B;<-GZ|=hS=YXtnAcD]A2DhCjv]E8I
m+4ry
P]-@03BPyS+mKsjMnYYL)(^0TyRjoprF:A&aXPH8Jl0Gvs8q?;X<9mQaJGB/OG""';case"sv":return'#Zu;:g~Z+$"5$iY]OX=4ATD[AZ&Q|TMU/@RB
R28n!T&y"%-8K(.WBf#_PVsd"%J,TtV5b.oH%R).K/:Nxxv=tRvPKxItG4[rC!LgI*K5JU
hA|b|iT1:OHC
7_VMPI_yoU6MWBfU<EG;.mjcyW+B4Fo9gP!H?6=wXHGK^FhFT/-|9LN3MF+o=%u=g2jf4ud5jdQ
FzU#
%VZIj]ei]XsbQY&qaTLfUvb,*t<n<4uY$5>B|lSk#&a2PU8rB]hRqB0fk4G$z@j-nJX>Y2%SVtf;~aij}vy(|iDH,7H8_[U*]^&k5P[FZA=e,u"n%4dX1^

Bc`EF2hB;x?NtJF8"C:I).2k|^nPA77C`sz"8TtRmC`BXye3?B;5:.OBM)>QhX^o
.~OPhqkRwA`5L1w|gjl)s*?bvws(A5c!C+;7^=-9aS@^960{/xy-6mECw^
/njx{+x!J1lVO#K.t:ZN}(MecoY+rTZ;[Z;6!9l&[,l(8O+D/J#`94c]ceC-zYooVYZ_;=
F+g[2|BG@j;}tt!}jS1tkX,fye:2j6nNWW*lE2
BcrNQX
92.NFf;F1wlIl*P]^mt;Y>Sc1lf8a(n~$NMuNq2mTz`|p-U)t2!9$mvbdzrV3zWEo?yJn)39Z@Xz]ufYY-B.57-rr|LC3xpYQvy[:[uR/~k*&r:L.ff3%-?)wx?hjeR+"i=#bl1a)}2KFX7"`101]3J;lZDv,NE+8PNpnZQ9rC"}q2-oc_m9@Ft.g)>J`)OT[Hue](eGkB]u3
RlC4u6,[),BGdo/NjI447lxFu!P9"fZAkbVt$%6x$#Oxics0T7c>0QrRV-r
?>0WXOx913U0X)a=MfGg)dFOSaiE+*>V_E+qfRAx>~*O^|gd-kI3l*D-@"3@Tcf]YKx4ggR-0j8Emvy}HKL<Jv7jl`qw-I:.-@@5cbiF(-"&uP5+%w3i0YRj;=QN+fZmPp5.!lWzl+W:cCA+@HCSjqdnZ?wP]%N&D8Pph14Ec3-1)*8eM:f_J<$Y1Ym~tWe(E!L*;zl%9F4&#*Ib0.XAtg6%e2N|9a0F`L#WGm[1c&*l+|Tbp,95F;pXONGVW?"|J)D-z$L>b+q4@&
@$P*Y"SX2rVA^XH$G6f>}APDga<r%:[&Dt`,x;Uek4|4mdCTm8=w7PqA;rE8aVRL1TD)cwP9n?|pVOmDzwUYrDbPgJpK[#$WahiQi^GBTLTnLy+
trS1$1NF"f;>B-1xL?<$]d}>)]-!7>,#
*Mly6gpX(%1G0GHJFe5w#S9l/~l2]V/*(nHu)obEHf:?9^?7QE$k[E?#/5P_rgPmGsP4C$DbP:bzi[$DqGb?O}j|sF.R6~<lY[c;-}L/u"7z
?%JG"Q[s.1}t
]D!bBGR7H}O{,Gj{+W!]J
:?DuI$b!M.WcoJ]By6GMXI!worVN=]cqM3EW[e8_uq^yO*jVV:T;-RYKXy`>(>
);GCO<yTxbeX!]MMx#8;VXO&OP<B#7VBgcX@k_1CZ+$)P:-Y{Y81RQ0?Je!ft:2YvFbT*+yN0O]b|x|6E^@&@`d2Z>S;PkV7:qjtm8bmrV$XCY$sra^tUlwD5xOYiwjj;ST91wg;/iY)mGoUljSZWELCK>2erX&ld!*)V6ixD<q)lT5fY&f^K;~gCpQQJ:7Y#3aB7k*;A>+sfLh`+&co
JE)@L+[Y*MAijQGf*I[4Xf
Cc?veTsh@Dh_[-+&tsm0H@mUX/qx8A)Axjy]9#W^1S!TZTm^t5ig}:tisBky5n&/=8+TcW#sHM&(lHCtlj]`{wFU
ae^BRS&R^XXu`h,V_gb^:msX/AFJoOCt2nHTt5kGZkSG(~:u>9Z!Km@puvH{xiH!A4eQ11?r95y"!UwUjm"!N.E{n&A)_&:kU#K*//D|>-85b08il`#W;e/?:$[
4*y[at70F1Es&JVe)"3/p",;-c<P[c(D5?;T[O:"KEP*&i:}A->A.E6|p~?_/:dQ(iViI"=%@(ecAgKwKh*AWmL=*kJ^`1nwtJ"H&vQreI:-N*z#l`gSUxm3SiKH9gta1VU6Qfkq#fp-CkXMQ}L^I50sn!+SUJ$dd"7oR4bfBYZ=ve/Z^]SQZY_kLz!3dh2_@;ZUvr+fd8X3MMc,PgRPLcqk<k3?E?>tj6P",R_6B>ulorD(1)3}U1(v*gErS0726:K,b]qD&xeaYD5ay
VIxGoc1RG7u7Xbmo84fQIsLY7ubk5|EL4*R:%5F2PnC)R0A@q*-0lMavTFDqahH/RDv@i!
8?#]I!ek!aV-i/,C=!^CHs9:imc[}>?.[@as|b)4~I.sAur5:%
=y#;8c:TVz6YI`YcHNXAu8HPpwdC_jS%y|R]98iew*$+xC)x1it?l.`Fy0hL&#
Hx4?/"QUl0t#l:xxJ9v;I_NRVnL^{p.q2"f;79l
Ktm):ti@&/u1)Rxx$vc+D+vmD<*G/+gu|8~4t=]Ils0Vy]uBId&V6cAOJ*?./Mb"_?z`F/5M06VgFZ~=iTr<+n,9So9UJ`i?9JtSM&%cMuLO*k5dBrsZPI1l"]5Z"IXt`v`-AF.Dy$-yCh
GWl?ek.g+##EeKGQq%2jURM+hLy
NO%`!Ua+=Evx3l4;n^a2N/9tM1k(4wtZ';case"vi":return'(X/A
^D0mEh^-q|/v.R%
lOx_!wi_+P:2^bOH,9;)9x4B;uiR_8(x_IPq3y={HxXE`%h?IMAW::=qI{Pk9FwfxSWuM(
n$K9%A8w6nm,_M|u.XBJ~G{:(5@,zu)o(5fRoKOVSwLD$bH.0+/U2tT4Zr0=C^I@nl=&uuL"0^27*Sby:Kl+Gt5tWp*^BfTMc]66g,zxx_NR.Jz"/n}%|LiRu#ILs!4=/B+`asuPY[KxeXG?3
3^O
g+15Lw=G
z#%~Cg9N0HZ>9AW_[
8DcDAR^u^r6jWHYT^*mY
RD=2Y3K4qP&_#KVP%_D#BU`QAX;^`Rkt?!~8So!9^wm2&/CJI-hrMkLW#_Y&J8%=&ilx7(YU=-5^W_T^[.{nS#2oUWz,p/RCcEF0WISRQ?=dt#}BUW0lX@3`iK;R1vx]up)-31t,0pAAeoFxyfP^atR6I:1(-[Gz#Yexowbw?7j_H;j_3s[Yzi*kYnOt?0o89l>&LTfG7TE=naMs}Des0T%g*!q@Q(.?@WvuP25ee4G9<>OlHO%s$`CDXviXwHJu9p<mDlx1|WSl
<Yic<D_Ah=Uzp,@0&~^af`:SbATdlwO>x,ZShCfR6MA<+)p5iP0Zsy.;qnkWlXXoL4r0Q_RUMGKsf,rN/5>5Ux=Qv6I5ha4gX":eSX@T2[6@b)7QiE67^E:
P=T<_B$3(>L+p[?R19>SXFW^PR&u$uQUNRx3bwdubxfggzhsUa0RgF(_5vFutnb:)x,e-SCrM0M5-pBVj/
p]`hcV9Bp9:7;,)m8USY?xwnb-Y!gE647Ni/S.(+1/N[$2h9fw`?nZWUP!9JV$
)SbDw0!P3>/orf);6xCg%a.0kZKn@V1aiI&m#Lm>kJW)32a=spOU_]Bt9G$owlTQ2
I0#-Eo&7"g79/
=yUj5R,^`c/iJxdD;k$0.+i~<VeKu*>urB3[#Vb0IonnDywe=Us!p)</-@-J8:ure09]89(5C2%)h>eUlgQFu3^P84+=.UFpP_Mm3fe-e/@C]&p,Y-aH&z<c!NhqqrqxT9NdJg&*xM6f6mG~t0AU.id0#in;"~Vyl=e:VHY>M^Z}pg$yFPre/fpm#y!Vn-_6_^fF:y<TV$X-;%62;PDy/"Q8h?wyD[tM`i;)a/rBQ~8V!-&A)m<4dutn+sDnhIh1?#&*ak#jAo?[:hK;[78eGP:/J<vK4A#hGvXr0w_mLynvp6.a9KKGPA*oTP,Mh`$j$8[g<%S5&*&{bQeeZI"<<vEk*Row$?w1Y/gj[zRu
+5>R^rx1tt;_95M_UC<ot"M_(::+9xf01XF7hc77Q4wz%o8$aM`*7VlD9r2O.-9#z?~@oF:7V6Xcz+q_$4;Z}N~wKV2f`v?YXyz&cUkbJ]67h7(gm"sE0=^0F3E^Qi$By,r<BKK[D#Nbf.+q+;xy7-EDG-L7zO
Wte2BnJJ[MH4OoxjkNFAjWOXy[o/j&
`+g_V<6vtyL$jj>#y#2+ML9Z7a:xH]X+Ih)]mS
us.KW.exYeE*j/*>gwBs*>Nt2{/m0>S;%*&cv-D[ipTKJsjLD9AKQ@=;sD$W`.(0q"xZ%4bCc&,mH9P*:h4ah,;MF"!Z<;bgW)KaNTKhk/vUooowK/ZPe"_s<-=%N@w!d~1xO~#"cwTDdV<h
AvO"_jKQv/6dWdpP8>Pt.:hVYxr#j/}RDMwgu>&2|qpDP9n%El_"9^/+z[)Es13+N/]lOxkJ#t#E%9Ii*ejaT.NooG`b9v&"n$DevH)-PK%q&=1vy.*ot"qg(3D#$ty^-aK^Zj?V@6)m@5#5ERiUY2oIGwGwxcn.R&SGq
Jgin|#&ly#iw]m(OT"|:ad1T|3+Ef$fqfSN`/9T8->,om>[oQ,2Ic3OPd%T<|
:x3T)FuJ@"=Ui]>;eG"&S>5[t3Z7M;MVc6(B~*LCnvJ3nn#&3m3wExnfFNP]Rs-L@
?_-cG9N`,"=%HtABd)6;_F2mS4pC|`%5!N?bqvjnYIt-~n]s[f$aP4/`nUbav%3poAO#Pg9<toUCDvQ;#t]v$52koalN;LASIZeL98-Z`KJ@d:|84$>H/.Rg^`.5s1;(!&|^2e{>V;?>Zc(;1V#uZrUV!e4*GX6^C9![VB"Xam.DhlveG<kGjfEHBoLLB2VizS+D52TV(/Mw{:TQX8Su<."H<:;mneGw}<g0=m-LF"w5-fQO-*V%2%C=.D$M^,A#6V]+2aTP
RZ/Uf,^<X9:"W7lBZ-y1yI=U/D(!m=j5Ur7[xmOJHt6`A^R)No/qgF
_3Uk/2&`!F/6MG&dSmyo*]/RSNT]&rz6]03LW06;^BE8tS.lRTsTWecRNZ?y]I
<}i,O@p<rAv_*4TT,_#5#sV@Xs`1bg_k&%sK6+Ry`5FDU[?U"&tN%S;pMdD5CRLyOSrTjyoJ.iCbVvNVS,N"1Ggd3=,uiWkp91#gADZ3BZp1ncQqH2QdF.i=Nn3yOJCNCSyR<8G"x
g&F)I@SNhdhP3#/c9]u;Sjk^(ZsTt;)qA
86rvnp0O%qM7YdPo%#)W9~Z0sQd;8fe|Q([
IU6(pa
MQ=hrYVj(m~KjApIk3z#O$m]VMMn~H3b!d#h="?x{#RuaK;`PL}^vr9^P
RC74NQh1WG}QCZTC2mEZ5/:G#G<(v%`d:2H:0vP=#r$Q6
6el^y#!t>su;Ls4EOeu
=dUk#p/&P^dvO^=Je<
%x*.DlkK,kTJn_b6@al=bKT+91h/FHbNaRQ2#>#t^RvKUng$c_06W
qTht"tfTY9UO&Es,fwyk&)7w]#xYVT?M,JM3L6GH!P<~S5)+uoC:"*?/,;7TaJj2h?j;jv7)%b#)GdW5*Avu:;hY:I4B5g8{&?*wJxYm^seL2IL,/n[.W_f;G)gL9qha%%iBlE9;Gh,dZoeyq,$O,
GAdD
TME_+JVHX81kSxMPLn+=2,-3{A684a7/SoX&MmYhJ`gSV`Vp!_n&=Em`jlotnFdt5S"uw)kN~5-Gj
9s}/4
x&&fH+ssz;(4:FjT5AQGQ4TCsE_.#.G]RnryG';case"tr":return'*UF@iaQCv1j^+r5YhN2Qs%F)YrF]6hPSnNRNe;eE"FYmL*,_6qJkEMOB|%:ET<jw6^1C$)3Q$U94^#yDs_W8hE2QAL%IQ!TK/jv7jio65uS[V0S:^:Zo6DZ"R,BM<_,`EOupVwVsnoE0e`z7~rD*TXen%WCMxRnI#%T6pj5ldcb.7L>X%6acOfLmem8]Y`{tYl<c?E"sFUQ6d3R.#i}i,Rj0NrHi{k&cMmrDY44q96#w4yTkfqwIFe}p:EkS;KznuZ455?Q/#T
n]/XeGmfgxOdv1X)K>MFxf/(`ahPN4Uf#dh)9[A6seF&Vx<?+no+q9onP2xceNkT_oX=inr~>6qmdgauHo/Fg!2Ol74~H{`Gg:Ke4^u6Q<x(7UZ$DbR%r,B$X``2%anx@qZ3`4T&KiI.1lcli|L9xv(bpMCaa|j;+srY)q[Aq}&-q;rOsK"jG2P0]st}[JU]Kiv"a^GSkM.@WJjS[!<>p1qWbG.~@q,26U&FLV9bPlyS)1:#@SuFUtX?Ap;voLr.mju[nSLrt~yg>MyJ#Ol(P)e&34siI)"#mfA:,Fi`)yAaWkKd/bgP&LRwM!.^`?
dJ6w.iE7R)#H4K)_l2:T{Vu,X.,/Wikf{;u0EX7F|(QRuRYG-T
I!H<3{8Hu=Sxnbb$uc
uTs6ed-9/cB_wv:%vo?1_Q1@;[{.q`Ly:y
&)t4@4S
Nilz,o[~Q"Hnc2c)Vj.oYZGRF*]
;pWa"g.g0}[2lCdf^{p]F:2"SU-%<PY!tz&R,QE][%B;mWaO9Q3j9Lr>G7s+UU3el]PwcCE|+wOOm{G}+0I-(R*@!4,QDMuN888PdtqA?3Vg3:f;D"VQ&aw"E$lLK0bSw;Ob`5u+E3#0jggE
^P$XHSu)#=tve4T?kj36fiU%kLgy~#UKkC0eKl:LgfUcYEZ%]bn[V6F<APie?k&3)3}KQ"Z>}f5X-q%Slo&W:p{;,Gt^]1mMenB$wVT7VBqrmvyjJu|$T@PdVLA`:RCuf@zX6@*1-<#:0oE^4KY+*;Tae#RG#Ir3M4RFj->aY2;kv[V#3<Z/%F8,ZMq,>$3*LuW>68{Dl=N9D&:2Ro*%OxfYK-Toz8(7uXXMsO=8&KFAu5*9Raf*U-.U$?PP:-zF7D-FWBDJl>_8EB6e<5l6a3{R1p[ZnDy@!/0`i6"6hUImGICXEkQeN%D1c4<.VK.g^3}5)>;DPS")P^tjDN9M./)*,MWx|HTutw0#%Eit?(hT]1O2G9AxTFYGq`dTG+0pg@egXnEpEuN&|"es<UiFZ#FZ~[TB7Sv+-$tX"n3%{ITYi2vdU""o1_njymU]Ib3#B%q#G7!7Hc%&B-o!W*DPJ*h!13gC=qxt&pyZl7R7=#PNRe;a4&.NX_!TkI_44R}_$g3<*@+ux==qYoaP~5T]NP@.
]3FN1a3FGrhto]tqm]<J/P
<tq52O_&*%kfdnk%T"W"b10pP,A8U!RRw%k/Ss/s7$JN<G%pb49;gd>Di454E[3#U`e1SlsJb#4gHKP*%-PqN5jNW;1/$@N(uO^y;NbvyYARNtD*r9!(^g_vRs!y:
O3LqUrvHn*&@b^cZwmREtE)EqE,c*2*Nh&M5(gc4i-mmVI:=JRM#&!a4uI6-x.eF|(Mb3wgMe/wa(!+j}$*O9f37f;a8nC?8[sbaK]4$I6DP)enL7coFn+H<
omsAEsIH;G:nT0=s1q)W^h/_Enm7q8QA
`Zp#n.Krj+XP1wv[1UcB1OW"l6vX/@;XiG.,d+*KoH5b]E-Z/=Qx;=b7gH@;cFPHgXP3V0OFq(kxC,nlu5
O{_*"0)1:`>>rdKZuO=bYNLN%S$|*im$BS!*Aq:Q9/0C)0_$rVnjoqaaCxog9XBH8T<rUMDeT(.yRt+vk`,Xv_5zZ1ROKdJ":z2U31t;$
8S;H/QaLFyoJUk&V=e:QURYDBS)~d
w/LYlq39Gg0e9Tp%xvP@8V!-9{H;%?5t-t=<LZp5CS7!"R8{jNY7,w=89?dFy*Vi#^A{m!D%<SgT>Kv9UJ;TSoN_N]!Bx2ik5O0mFb_9Y_a{?PgMhleb<]WIO@aoKsUtQG

dy7Y=o4XWv3Q=~S[^ax-Os_s16#0h!xN(<14JZX3ojHUdKH*m3%GT|QlI9J"Kgs*;p]jJJm%Yu?:6QR#uPS*NW2QJ<Zen,YOinqK3E)g$f9U(5;v@IsLw"!P^i,6c5m/0qG#2|*$Tqe+jtDc3$<xZQ1NZ6Y*Mn3-+r#E;PEjaz^Vkr<I)61s#h6eepsL,Z,$h:&0+q@#9`+`PsW6RcOQ>!Uq%uvWhBikE#9@i`
$L05X+C#[ve0[qG-Goyul9lHYdrB~D-i~>.59Gz#`%WC+.u]$A}
&"bkAOgwQle9@CU3%V?Y0cH>]/

GGh,Z0Ajs.X=RqLBC6"4~5k;#f;wN86
7y7Dp,(u~SoL<EmD0AS)bD(a65;5a*y2qQ-.y-8bKCc]mq##y,G9:w~p(`C0F:0[
%)dH7kYU#u`+ZL
A:{GB_/A0U+-5kX/0A89`e.Z.[dRTR,r:^ZP/27@BAA?m%gf%PX%?d1?SIoz&VHh[j2[9f@e4qbCMXHZ
CQ!F:wJz7&ro)@ZZZMwL*)!A<G9n8GP-*O0}_P^S(Rhh]37M=sni;/)6F6^`;}e>Ohs/]pikks%)xA<s3~Kfm5$T0j%XI(:Sr6WEYaW-ID;2p^1vY27|4e&1[uEZ.}>)@n-fYH/rRj`JqH>et~)+1%=Pp}>*[cMI$2^XE1X`m&0z6^U<BzAtX?_PQiwL*2Hz6-?mr4C=qPHEPux[Ui
4a,tz/785WS6M.:iqcfOz&&?wQ!P5@2v,fpPm^cG?".SQ^>*nG[#"Ou&/-k"oW"TTKj4VFYyPtFqDioiSPku|<^B]-V.SHGFc,Z-BlgJu[~>LYm7Em~M<5;';case"bg":return'#ev@qaLZ;.C4kh>"$pP8g-2@tH_sZM30T;chdJkCjE,P2u7RDsC
,Sx`
jmPAx!h96w0evHyZ_tF="nOT<i
aB-s3vM
hiUjx@=YVH@77aK1.HLxz`r`6Q.f1n;c0WJX77|AIm]D5N-xZ`xJB^;HA7&cD`M"~+Um=?#vxPKo@42[xx3xRBD-APx@(x9e2)_UP*_kcQSTq!=$7=06{S3Y$2?>gJg&O0_<qW7nColfUCYB<1)7<Xvo<^k]XGg60g#Se#>)s9weh/iWQ
l(0ykTRyl8b)k^kJ]W9jWrq(*Zah`HeiCxau:Ljysg^7>[%DzPSNP
&kj!NSp692ZKg7Pe_TwhAUZ(z];yNqcjWv3@1N6URv*W
+t@If+u(*AS&Tk@3ZexjF8oxCok87W!kZ)T_@r=gADKuhIX3sa4xP|s#qqiC2R(`8"u7OgU`F$aMkPNu6L
A0.?)?_>N$[*/Une@E,r.wVs"La)UCDwnLEPY({Gv@t[hRyR6)Ixm6V=QVYqE,i%t;)9KTMZQMgRPst$*d3RxFTX9=itgHbc]r3u$,MQce~Z&x2w{mFV5${Vt.Y!`i.ZoAO;N^g[wr<%s&OG)OFJf<`:axTPp+)I8CV0;Ec0:n+/hU^m07F7=MK;zWB.n!u<pz$cxp*8#WrSWf}ur$coUFA27)
W/1")M<1koap-r#XGIIsP=](1ATgp<fE?r3mcdNU.ChFcfx#ZDs0TxVm>Dsut7UlGKDL]j`!I^Su]B8~&T5w8anI,#WgL2GP,UBU?}B%g5eSavQTEPr)CY5LO]LX#oPp]D:wV#T$cWGtPOlHtDHxVenJiGj8[n.}F#?241%z*y:jLAn,G-NvvIv/D%rvD:5_YFSmh].n,^@Kl$(+&!+
*@ld:nD@!$h,ty*m6OX0D
1sN
jC1(Sm$s=|Y?9bS~qi1nVy2>YzIojjq5D|&M07rmK#7eMWaw@HFbK"5OQ&.}?).sqrrv+;dTYSkoXF$cy(;qZS!yRcH(W}.U@2RmUlx~D/J[ipMt$3:sq
?mpta1*I<P@}KK`/`|t:UlX(;i;{jqv&p{u2v,L&PwBZ
;wZ;d.XNto.iAJ>EF#aT4&2AYi"fl2WmscnDAg@eX
5:56)9TA4-}w7lBFh8KZ,<NbS!vnk"G/U$.w-d6$txr8mnQ2z,eS6E$fQ5jDp;.1DY5vpXW3wu?AW.%wy.Te!,os^
6Bl5M7ZB$B7GXSVC(gXO".(L9$QSI@dp(X|M;r7?yfY(R(t=1B#8<J3yM[cb,`@5;Rn2i1}w=>?=>sA9vDS_V6_7v
*ZO84hA
fk7=`I9f3J<UH8@q,ttY
cvfrdC9pV1diAv.w:%+hZIWEQS[i1OYp5(%QR?C>839(.3#n)%(a1gC=[jnO7},R@k6g1D;vQ{q2tl*]d:VT;uqJ1-nb
(aWoi/8Q~Bt9KRcsj
If(h3x)+K0S[kkm_2!IDLWj`hANhmS_1k_>dlq%y8/m%0qZqn8)eFSIm{&t")Sep1lsN?p=h*KeWeXj"i0ULuI,uH)%)7d[fSIRy|DU-vdR6=#cP1htH1"!8#N%S>N#(O)3QH%%)V8w.^F_tP9u(KjE+#DHL!CEm#W$HWTX^c*r>>38"KRt5Kx5;>U}b~K}p_h`s+xXG)_oFR`}W!v,0i!qSKq"h#URQYdA=`FIV)0g;0t_fLoXX2QQ"DZv_@c%%Pyfs,UOt@,92KL$)p"+#IDF1h6!P#u2/m$~ZE)qd[eG?W08@|YB5&;GJ=Yw7*p[L*RRyR0|8p#^%zF~H<VN&
k<OOTOo$v[Ra;>kbjP9(xz2MXJTyEfNUF7A+mbR$A`_j!!F%
8v%t#nzGSi*3|>6qdL7fr^=[n0ocG<1PlYtR<e3[<!$%!%A8oD21x]$ko=-#WA-qQIBwj,^aNnQoKXSI~HDwnpNSHTL?N*{YAd[B"ka^<%[o1"$Bse@yQZFPYAe3QwiWb_?/4u!A|F.-i]n7SnN(:sODd+{v#ev%4+3^jG+e!vVby``6Joh0E@,^`KRF#b:Cn`@#eXT.jy`+~a[ZpA1c]cy:4=]h$5rOqK@bza,.UuKhq=GyUcSVL4J){
{*kyup6aIW8eml3[+;~hL&M%LXVpc<E9X>v$%7CUxH*175a5N]c@s,*c]=.yq:.IRm"1%Z~YP-pSdnw;+t/M_^JVkx.#Sthc9_og[yj-SRn/sBv7Hmw"[c=tq_ax;#[,s&{tYS<>/vA&A46yn%<%ZLzklDGR`iEcG5<2kET9fE:9Y;SW,HL[wq4O>v-]c+ec{WcTmv5aq8!RZ%Ce31O_&0XowAO,/WtLe;d?Ti%gEI:q!6RtD3hM`-4iRX$b1-4H-VD7sSe8}NJ
Zo2u
eau#-]4A,hF}9_xDYNHfrL#F2c/l.:QFjN5UtTfY[h4l=1wTNH&@vq8VfFet+@O[Sa1,/.U})MK)z(N&@?Gz="5gh;_LW>/jFwt?J2!g>[j>GwPraY4IMESeG>UtJJlx"VQU("4=;Am,/R8vhT%
ymwUi:CtNDWQQ+8ILXW24@UZs}FANy=4jV-r`igm;a[9Zp7JRHT)?l(H%p*Wl,ple|s/:s+$>E&[^%5XRok6KG_AAI&^!Yl|CuZ?=w_nBzNGMbwtyX6MO)fL8PJ)NEh#0EW.f4Wo2srY$hVFXuru&.,~C)Lj]ws+Vh
M`kE_$-d|v0FvHn/1DsB%utdB7b>ba?Sg8ER"Y5DIQq0$5[g6f=sl_:I=2$L.[vG!S8?b^E4xXk?PACmg7$5[N:s7%YAsJQu8^s%QuArXEpwuVqxyXNG9(&Mu+X`/<=n}"(E^KXSI1:qS[Z5|;$t9U&:";+
4$xVrnUn-mkhL>9>2wPl6],o+l;]I!99V6A?(Y2h3FNYUXo[fj;S>:([!JXT?Q4#-U<a<"jU(^Lb`pfY(r@[c8iyC-VFd8w-?*&nJa}UW#$T%Z4p^"{HvS7?I?>P"lCe2^a?OL{@mqh#sa_]1cg6>6G
UeRN7u3^Y8"#q`5n_aWd82hc48v["BS<-jhq_M;9IL<EEOh7+x8vP`9.G7nV-G56
iUH*XZM/gL)Ho|ESx]9)OyO2P{uYtm$+I=^n$n[;McE7_.ZTfc=hE.wYmYq[4$+@8r`yf9c_1AOQbMGdaw!u1:"&i{9xUsIgoO.|09l0<>7-`_q#px8g<JA{XR[i1NOLF$7#=1wh;B.uulH|x0Cx?lKT3DrOYx`Q::Wv#fZ(c42r=R`9F|9[)|q&w3clN{IlG!BIe;,``]cZ%TWrOJ.!R
)%>%SV?y]q&C<-G<Xv
"YfCv5~(N@o/&->@pYLT^]`I/vAU8g{ylOTppHrQrdx3@Ia*UxX&mS~g~)=MQx2+)65w!7(b*fJHZx1&]DMeeF2R
IPD77>="97k/29vAIEjW[irIp17*RK>lV{CMTew"KbB~r^qxf#Nrv*]SJ{SQW
KX4=2PT[`CXgD+K(UHw2qrI[nX[Z??GW$~Api|T58`PtBiSMTiizm:y][mK$8)w<Vyk+c7[&=mr_BdMHDnU(u`+if|j@EVYRO3;g59-hmoP:Ve^])v"OcmmH2XDo@s0kO8_7';case"el":return'(h_;:aMDY)QXkr"#TK%N5S!OH"5KIBb*=Ig;ro~_`Kc.V3B"fNi!y&W![B5qpjGUoBA]:J*JN
,t
E7]LUCac&^^WNmE5o(R/u44inlv
EvA:?B[4hm$"FFVy+4#D>8ynv:6XFA4]KHbXG|se)On3VkG:/5
CCL4hB{YL4,Joq{*YFuDb7[av]tI`BoYr[GbX7fX44[:Gn&y}wqH#X_9cvzP5absj!GUsXHnA_b&PFS:|F?g{=&8jK;eqUq+y@OW-fST^#5o6sgJ"G}bB50p^&#qTo4x|#V>_IpA:$68KQV+?Vp@7iUSAk+MIVoEzbKGz+9?-<6d~w7WqOVJ#S9b!"%Q6U$Qrt{oM)m*<giV<=T<h8MdPF//!)$473`[BCkI)oj=u,##!;%-f>a:<#NW1!NRij+Z+!o`:tov%3|J9fT5yr85U=`jZAh*~l787:iCGm/@l(]faTdHts|:13.#rE+%gfp(j]?w*bb>^%6pyVCt<8O-U<5v1%
51T`PQPML8mC]UKVFKV
%NQ=@[eDAwQ;,kx}pB$IvY%z"RS}>=p/C`iX3idiE5GIxM*:?4&I)l^{%J`kFs<s=0xEm!5>[NdwD~deZ>B
_-&>o$ciEYg>;_Y<gsFi66nmEO=?@j/@3}
uTzT}^EgFS^L8431N^_`^Yys
_hEu2,ET^T6P/h*IN]wrCso
=,5;N4;kj8d|"CgVl5^F!Q4~25?TpREx$ty:`Aq~eRP[b.y1@N]:*B1KS_cBSdLPP/d,%yIOVkR546Mc2g-xyF8T?##]K
lBCy>Lxvi`^(vnVt=8g.+Wgv!3@K9[n;omx{Os;n*]4{&<X29ilI9]Ts-b`OiWFFxE4Mgq*#d;w+gxBInX2wNMWeh}ITIuWrG?bWRvMPSW%j3+gX&le*C2
q2*t8+%D/6t"myvF+8~A%"*0#OjG>/{v_
^4/T/(y_}lRmq2c=G?OViJ9+k"7IPp2yG7>y-xz!y*RA5IB^gWFR9e`u_s,e9BCGs&kUTk+>!:Zq/"eVt.A&<3AJkn@(vsF${2uVmmt1?*xxvc^y(^8^t#no.Tde@[_2H.jBk1E[]vGNr!z+g)>_OE"CzD
e)#9=y+:OFZ)g1pH:,KBt1-_/[Vnxw24D<43;+"g#
JJizH_Yl,igzr1%")-N|wB*wdMgYhp*Vo.q<ZJU+Q[$`,>$a$s
$%)eUT9LIXW3]4"Y)CJ<Ku%<~OLvEt!N%SJ"3c9SQO/&3giU5q6P,,Qcq`L//p,YZ=UWH"%4B85phh)ryniw(W.ZwW!qY9;M[$@<6[ni*!ZGAFd+>Z;1w7qHo]~+qvqQN6|T@HZKjO{.
ya_i4tL:fa`dJRP@8@sSo[uWL]%kD}^YSye9*6S^OKr5lAig#{@}ZuYq4LZtj%!x[%.e`Pl!"4G`"I^*w[Xg`EZu@Fr
*8ig-ec562RMP(%`"KZU5m^]SfO<Y`pZt?iErE5cWW0SgC_PvcR3s;Zw72(83;>AYn$wuE3gL<bTZV#t[TBG1g_e/7$Q1;7ETZ7if>d/x8NcsA2(){<(7y&7YE%W&aS$VzLd-pEM?=.%&4JjPDW@fKyh[2l[&p&?=5>Z[-TqcxV;iVwm`Al#HRtwa})#KO8=i;akYgl4
x+*X"n}UACkk{>Q`$W<k(2t
jY8PlFvh9XvfL!^TCTpm.pZWFIk=*3b(zeSwR7or[#d){*do,&6<J@RVw?Q)5EOw10#aF,{DW,HGU4RPHCX9ESU,wA~g1s1b_4aj6=1E
MNQaPLIgh;H|^N&m56SIP|iN*>Ua03tve|s?17
G(1+zI22?Dj/TRZ
2c~_cIH,"Cz?hM/%2UFyN[=!0T$U4Ai+5Wig!(3xs)n7U=!a5[;*2O~Cy[cYYE3I4&5IG]b*=!djNjJ/|qZ!Yl><h`N7ySz?=PM1Y(#l~k5;6]#H@-h+|Jhb:juG@=nAJ9`SO-wOC/lZ-(V*[t&_}gr[SM7vV-.HsWuhnT]n"XAU.6rHcr->I!/K]Ym5QQ:72u
+@Ez>$jM%d5roBTT%V9p_VRB3l2z3~"gtqf"1K5D:*<<d3%]`/(I)aMA]m!*?rMNtjV:*4FE(FbR,6.aigy_U1wak7vikSGMDD&CG:Mp5irviI_3w>ljU1&SOG06LzKj^p[>r"`pb9"|#C#Nd`I<^z%_*^BQ$k#0cwe+?m0ct;Y!8:iab-3PL:RYA>-|5L<0TnWIil&H1Lv9F4$bb#GwX_W"$);z&+:,/:+,7O_"Rp1d7O1{UaAPLI-gQ&cXc]m[gUkAB84YH_jCudKqj>f_&_r(VWoyOPXb]}7I${Lgu>,EnYJtRMxG5~
+4nygU[Vn0e$_p]dVV.I"Wk`](pV7g=khe{g{bz)(o_*>y?01_e(J%G]-Vh&wdWs(oXq$YRyF[,>k;5bS->HNcQ6b!Oy?VK`1Z*q>da@]53t0(bq9)]Y[
3AghO.N:[XN`;P!%KQJ2z>ylGg44vKIKDwpA!hD:gL9^[+7IgAh>)Z@Zx-aiU`!u5]%mVau7^
r9n/Csyk$BK[$T:U4P~p+dwhSG>2aZ:Y+Lm$58[nHcfGQGQA?=y1b7uAe9,%#XTP/>Sq>8)hAY62K)M$w+*3!FI"P>`?:V&,-=*FB.O`6o&ZanI/v^_0G4X?]blcR>eSWTn(t><4*62g^3w2OX8V<>/3Q9oa%"5B2"y14*#6Pm-dEGm<Pmd?mLj7%3=8M^J1I,1E]h)y9dvl~0M(4EoJ<NUOk!D(*n1K"
kR/jRRuxQbD$]I-X5jA[bd(SA&<jNVB<!(=.Wp9K.ZrgldPP=q[ciEW2Ol3_jfC:F1"fvv5vWNtZ@.K@}([!wtFS}nb8^)r13Wf>QKaFdVh4E7ft?8q25b,q(I.3As;t!2Cn*4#?:[V0zS&;UtRmAp&O}a,.T/xl-B]7yyLn$lnxhce[&0J@1C:5ujJ3M(bW1n,VC*GMz8})nv/h`2Jo0YkXTv
:kN;)Uak$Lb{c^?[_Gu~#{owk>T0e{J
W~_-y<g^V3J+vm`G$hME^bT@l1$+ks330o.MB2X
w:BZ2ZGti7vm
$i*L@elC]UGPb<BwFQF&Z3Ak{awa<z!1!LyWwHJb!TLy:ZL3^srcVUV7]I!p%lDHSc5O?O"&wq?=i%@B|RNRVT*B#^dlUbn?|[
+^MB)<jiT>NF)z>G!?BJDQ0pSS_Bb@v61ZQ2Pw(L90l"b?9km.e1W46
K(KJ<yhjG"buO?U/C<4IqX
-9*j)x)#P=A$}5kT,YgSUa4.EL!6;YH/_Y=39Cj]-A`VUR{,a`#X5H9@~)~tJS{VyMe8rc2ZMd!8r"buQ,6fFq3^a_g[*J?hQ;SL0#6ALn7%)$|x^taa{/"&MY7Z
1z8<"?$+MV^@Ej]_X5*3l)Po$j_VU*n{Qvwxqmf;r(1M2z
:1:Qe_+=k&+P@$Gm0?/A;5W#n>Wdzc$;h2`NIP:I8CpSZ[=^}[b"O%X!s
n/3RI0gjHt3LT7X?mu?g+/};f?O>~jEIQSM_PK_g9eJC#InD&`9az]Ne:fx^tNQoV<X
FXSoff7bgh9c@Fuv4v;9|/ZtYvBDshgcCMui**wuTIvBnxkM&4B"avaodl#3k,qmvv5cpl`5]$$cH5=0F&c<QNufh0sH/K!<7RY^B6pR%Qdv8@pUYNM%QyhH_suAxgtJxZi@K"sYd]<.z<?_g[+Yp$f329obA>1t/X3I.B)6,6ML]">ew%_QwEE3tj1HVu,n/<~5ap|,!/,G,Ba1qK18w!1"~YxZ9rO?lF(Ps7kbum.r;E_jxd"UEuUR(l1$_y2o;.f__sVX|wxfh&$FEdm)eotgjcD]PIEM@Dq3|F,x$Zs3+59-m4-^ivfB"/Zqig(w
;@-.KbMEX")wg9x}vHVlZqAH!"M+YB+f*"&XZ,eT](>{3mE(^4<]U|GW(cPKE?Wr5+_IRL9HRD^MC-QxE2_|wd9^EiPABm@ijw:?["X}%W6M#;dd?bEXqm"b';case"ru":return'*evALaMD9*5sT^~#vn["8;pnF85pVMK,}d3e
:O*d9O=w/j9k/nC)$ywOfS&u6Q0"iYyZu<Bt:TWvM>[<O}8l&q2k_p[D@+ay?@c@;7a$a~HjyVyQq-ao@82&yBRWG!:N8a-^kNIDs&?@by76m]t!qDpyhQglQ+A*yG[vsE
V9KnQvBI|m6i)(B&|9[KEP5g=IdJ/ERt,)gB<VziSNT^lsB_wo7_NK~IX;8!~,Jw"v^nQRY.&jA5AfJn@oxq-)J`ipV7v=bH/Uu&ORE&c[qpMGXp.7p99n{s%K0.y/Wk|h#=jvCLEEjQrADJ~#v>5S8hW3_,
.H-{/?Yz]U"J$}ra:W.xxaC;4=Xuquql7@4Dk.bGLavC0:f`q71]y$k^Hu2==`K4p-_}#Lm~F22_eikO-kn>ZkFU;TIBA$O%F>HmUs_#xj(4qAjLyk?(<*HJ:^U[>d;q"Q=?cr,PW#HIQhCF[&l?Y0.Y1zep/=9])W5c)}jYORJ}XY/g#[I
o.9501c2qty0W[^T*hC:HvGn*BJ7@:f=cty=3j,LUJ8FT;&kq_=9c!<SELm(E=
6DhnE=%s"G5Lnpah<ug8@xc1;92G}yK
Bsn&aaC^v(6Ojj14,rK)M!)VKkBxweT)z&ZQxS`g+5jD0<{gXoo)jZid}hfV!E:2DKQ"L:W+4mdh?coJ4Wr$6!ATB=/43kiS$456e,gSf:a`+n[Bt+D!_+3F`ApTA5KfM]0Kl?3N%GK.z>-#+41Oz##vVQV/7Hfb@JEd8YKNdu)2tK>1@!6cw!.Rt6TtfErnYXl*d_^<R1
XVFquWU]e]&."K:geSo/?iZE"hF36n&_:+]^vosFkCN[pmI%8Z?-@y/VxHH,Zla3+hq13TYXxZWiG6(lg{>#rXcr1Gt"jV;~k8W2fBMWLf^cm]X^5_
"85A|W!*08"r=lm2iIYt#c|(Zs?&=,M*<r6PxZn$)b2!0i_E7rKcUL:226Y#Yy-aewmd`O2SHN<ZpMLO[IkHL&DQ3
C)&+F,~H_cUnkgNxht1%h+g&PWx3~B8=c4b+3;b2JXb[Oy)2}b!QA)#di[b_k7._V^7v?N_ig&d5QmTnsr0vl_ee~0KV)OY6=>mR_qni#1Q#w6&T!]+nXs{3N:gA-AmT%)/lM;T1r>/whG"/S:PRmZwb~HE_#3$O%0]*>3:LP
!I54J+iM,ur"
pj48+&C75-Xw5}J>$p"m..k73/t0Fp!PTa,=op.laARuiE4WOkW>whqf.h:(:2-43V-iZAz)[I%H2hnATUFOvn9[Zqx119o@X|0<T%lm%V^Z"[99LV?/[]:k[C*dIx7~(3I6#oL`W,HL1tMUMQV]eNorKzL2=ifsgP0APh#5%2J1G=OI2w`!2#0""~.9KuVSy]lct_6<.A5fR<5=[`Rj#M2]]M.>[6mU04k8$|N"1-ew+%!?To^FZljS6aV^oX=MRNlV/h;5Vo0wcL6OC"2(<XS/Fw0qaaQ>NK9PsODZT-$=]TH|iiu,a"0YsV=?9aQ@aWd8`cm(lJ4D1Fo@&(PgTMeg@(R(7CPh.r2)^n5tF4CF$di;cThoBu^u)nq[-f8h.Enbw=F
:Ok#^UtVgK5SjP:*MzAPWUUd[NpMCsQ%*^o.Uf.1-{qlPNX9G:LX`=!@BhT=2M:GXLgp87>"pa(6k*T:d0U_Ck;Z1I1r2|GInTmF9hPWlXpA:8"uw@gte$:]+2u1n2x";%G~D1Lm9."%qnp+U&X^jAUDnR]3Rr
=[XZ7F2cZEzOIVNux,seL%O$q,H6o0B2KSl0XMa3rh,SP(=?SZ]*d5D_Y>.<Rnu%22354T$h]K~B}Vrb6]-:BlPCj+Q_>"b^w;@UEVpkev+S|rbgKR:T?)dR?XY3c7n)+#Vf_/]FsN"_C@zl**R7`#,Yi+p7F^C^0Kfrle:KuJv)
G5P)XBgU!(8Dk&-s4@?nH6[>[bbgO/AwjTudB*c(dH?yl18!Ez3gP$iI<&k.#OeM.AWU[-31Zeyl5qNM3as
7pG.Ul<VZ.5pSGrCS8Lt`hAoQp$%=&i`GGsFvMV=4}Rtp*AYWx_8D|sevMah-5sUI|?M%|D:L`-*)(hiUc1FhVGa04BWSF1o
w-x>^oI"F[4seP[_
1
W#.p@|cwgqcXEg(u*oF$$*?rC.KdxJv?,;:d7H1
$%D&h:xti=5,3B@3S^,e]TU)qc)7"axWg{
3NU?McO#;ikJZK4[>xro^p%]_mr@.UQqoVL7@R*fQ@)Ar>vB.Z`RKlqFje-fb^"1WbWUUA:3ia7&E1%E!*=JAlz)k+!.a9u@>O4?32pQXwej~@F7UI]>G53,[x7&7A]K1J8*Ze|/<uB2aOU$?3er`FF#?=tWgk?QQ7WP>kNOW+BB]&SxLm?^cxEA
6VZ*O~k+(=IXk,ge<M:|T+qI/Nm:Qt=>rNH^d
:?>S+"XPvpm9DG^s4[v
>3"il%r|6[m}G-kv_Bv;/^XA<};`Iv"DX2Fgx:D7r*([WLt|Rv
E]|<l.Fry1KuIV}.AfGO(N&i,a---,~=`S_.Vl{*rpTFMKk#$$M>a*X,|ZTL)!6_h%U<zLBER<c2ZL-RC]?kFUZcR$::d4_-?h6i)j=((+`vn@X/w^8qG%o3TO-D>2"s2(uCGn8m=
oE&87o&6I@2itDB$0D
]qsP5lS@3(IVm,@+sRS8?.#s])-X%_(iXl!sJXeJOC&1ql9rG0-A6HC
3m=G!B[ZrNMz8MY:Qr9v?nWv^18F0}
dd1gc*rPy$F>B9]YXao"=`JoweYFED=lqD8o/D2RB"6;$Xh;^2H`Rkc%WJ%i]e+f$E
Zc64UM`>Q3y6)F&:BmJlZukg<)iu7*1H5s_Om)2~<Hy!BxPyw()a5O9
XEvDFE7SKIMUs[ck[-v1FW]GB
a$ypV6ueb"=xp]dv`Ek2ndW7Weox@mGD2@>d[m=-o[nR!Wuqn=Y7ANNT#t#|i}!@B9X{?
ImihAm*HQ1AJe(dti]*YmNA4%ynOGPlFcD!G^SbjK,s0K(@?sYXJ1P3cuye7c1(Cf#3^G~U[KjvZHauqS6@,XZ<m0Hme>i?;A3[nq.+!1ssr*2&eU
puWMV-9PLB)">vD<+TV=?fns<=:hVJ"9%bLf=aKj9y"1<#tmA5y05Ya8FHi25iR3J7hbrTI4L.t.`Z0tt*wr
Uq<1yDxE@B^f~U:c~Dzt^,ZY>0A(IIBX/.k19<d2Probb])[@*$WZ5~RI[.f8O3?GP(4hp1q=fGk`qid_8ZVP5_D<`Hu2H/&I7Iap@BX-nR
Jkg(kH$=qyFmdekD&8uwYKW+;5<cS;XB[qVb%(O8pSug@i,ely[+sfe_7%W]`01ZY5U0r%1ZB;I8&iRb+DObk(5rOv6aU0x_Az(=<2H[=s1S8x`VX7Xh|Z[drY0vBB=a8;[MkX2jD/L,PPP!W,pp-7^d*d7w^P=K-Qv-CDff-:lYE2_lRSDy!LLLZa"B`!=7{52$NXt1Wcy5Jit*WgX
o3Vv-dhg/UVZl.ix.E{2$c^0?$gAj%TTUJW)2e8kTmbG
OqX19"TI=)H65Z/+^u-%(@@,C{#y8Z`r]Yby!<f]ba5/d60(Y7$2SmZpy)`#pmR/fu$+i`nj)+&EIymhcnY-3-Uz8"!BMncOya%lqI@(f:ZI44Y3H_uCRUt-3!xb<<Gud%V+MHa]b*B3RPS6A,+QU,3W7N&A3lDTi%Kv>pr6rSIt_ZH0u^m:hc
|ACe!$vKmK6P*yT%8pKn*3]6dkxD&:m]Bnb:`P8M8?u4#e^"+Z_KBx)g),rB@YS#V2K+9J*aDI)Q]iU[6p[NGgX--q+_]VtD!2uRkANExD"WKsXywC(e:y#"^=`uRFmD0WF6dH7j,Z,HUYO7ARo7^@?jiC6vEY=R,0]r"lYe:)P]0+fz"a?';case"sr":return'.c0ALaPp=*4c$^~#vNFT8.X:ao;-
)6S
wa2}i|7,:4#-GXBW0b8Xxk*,2n;)cE@yMuPVXY7;[<&d_g**O7lfEJ0QKx?!UIF~*-=+ATLCdagHuZNBrf>l+z&r2y][E_.AKhtmv2Gccxy[1UA/)V#?Ko
|k;Bh*&.[&;bKU#mF/[=GqN^lIcWwme
y;lg~"iMhv?J2AUYKDsm1khr;mMl8AkS;9h_pVKn3Ls*gtdSw1C$S"l!+Oj&t>IV8*lX4TrdP1v3F&=?lT$S@M-&-F3ln5nAkUX1%1]TkX"Mgg{FJV}<_9|7IB0HUYMdq3_;b@YEm+?yq
`J}Cjxg*yhor%-NczS|h5<Xac3xo!.(3z)+awY;%SWJl6+"Ru<$XtN}bM$D3JU;(:1W[jsO
*<(eSp3"9WBhAcY]WV8?>*lZNJ|"c.3;dDlir+cT*bp1ak[<HR+6utTk_l,^C.P.HX{cGlB=W/alk_#5?jOLE^Th!DOSH5n[m>xw[&X*3S+Vr%L#0lD+"<1)7l:-7Q?KE;l"A(hInc"2D([!p]#.]"]>9<wOPTcT_lX^fe%oDZ$Uc"%!5lfD6MXkG$v,0]}R`wBH4ex<xp
=_=tg,YU/):Q9Qc9`pQtYQ]iaq;:%Ys6cF8"$Kd(u|:8+wyLc$Dl],m<Lyx(*a^/YE%?6KmY!yaFpt;<AYD$rH;U`)8N*J&MQ+_y4k/|d>rbTsALK=&aLmO
tcpUjnm,yDQzU5TwlYBnXBRbKSo)^)*vI>]~N0^&T:?+8kDg(R6/wx7lb#nO(60mnH;BiEQP$=
OTRR!7`oYG{"|g[-|C4wQp0D+)(b$?*P_iYLM8TFH2pneQw32D#XDQ2,8(NP)V$,;mKbd*+Dpl{j/t-7^k%)zMm:&Y0.,WoSQt
4&J~u7S~oIY87
w(6.EFp6l=S(m,#<8L/1K
>)9O;J]e;,&!)bR?rDv>#n@@^sA^^g.3A/ere$y7Nib38aR[aJ7kLG528o_Zd.ZC$CV^gxL"JxG]&:gE#2!kK#xg-[RWNMsk+.!iO
w%DS,L$mG}SaX)cC[.?-&y4*ZzO|6hN$$ot$h_WeZm%~hTEQ^b4xe8b:$Ql1A"i/R@hPK.%kZk98eu#=IM<Do7)fAm&lQ%t1cCEx=|y3:,l2/)g4/x,M&%Yx"NE?&9+ytrY}Fa>+1[+65A`-wKl11ub)s<z&f^ahmv;iVtDc*vb"V]84-&3o*^4SwE#%7wbZTDC5tcK./1TK*RMY#hK2U?OW6uAl0R@I@%KUbes{Ho[Lt{XouEF/N45@(GQz/NE1.mKNC-j/0I3U&x_}Sht"aIkpi*kvv/:&*FVT>(+9A_N%E&@Q]&i}
c#Q
TC#<3g!HH-B
A84u![pT"cL<p(:
9@D!@D8>:<;tJfkOUw1fkb3RJ<r@=u1kU(}&4NPbActBa#k+3Up%x
F7WW7_dg$H(&f;sPR]4.IaJDC?yU7b"&{SRhRg,Cry2_>`@8|P3q7UjkA"qJD%@L(3gVtcxm^s_,>1Lu
gr4*ho2XJS_fM%Inb!fm&$mp,4Ugl(3ptRI$,C-
TlH|2Ay+5{duQ(Z=+}GS;?;Tt&C~JOh3tC5^=U0P=wm3A9l%
E0^GIIp?mg$ZFP`]Qp_r]!lV5($d%((LwgAk8V`g@ae>Djk.Y<R+Om$;KAR-/`OwIl+:_HNkd?tAH5Dh%Ax@QC&1GL(0i)B&]5SesW7%gBnbM!y7aQ]!?;c"K>v!72S4Lp6P*[tR4X$9D#ZH"`Et~?CeFL<*DG5rGSIhPCm%NTQL8rN;oi:[!YkqAuc?]]?T!)%KmH_4C
eF`L}bXj^su@AsO
(K._x;rfvAiT,O0hhrEE!(g,!26JH9`;2Ke
Ln8j*KcUB-yeETy^Vx,V;wpD~Af#Y-wE[9>Zn2o-/-)vv1J3dTfCS#p+.h!Z/LrI%@vD7sL37R*LC6,hjOX>vb,Z-bFXam55g?&p!TqB_3rFSajgkDh)D>#&i[(^b/O]jJM[&Rr@jZn4s6B07]F6mYDB):H9iU=aEkZH`ie]djHkXloYjnhmcify(=g=~LOrN(Fi@O+bGn4rL&%4#MvDu#/[=]jZU>CW+WK*3V;dQn<D4]GkcjVJe,Pc.:_jL[cxu05.%w70w.K$9%{fCw~/-X"sNIDlXZ%1@o_b(0H3*:y1(drGa;bkZ+Gm(ZD3~e`=3bitR?B]T?}@H$Y;
XRO?T<>ASxj>22F7P?BKAOZ$4]x|Q-@>4?C`7:4tfNjwp
Bf0;pOJn<mQqBJwfK5K(^NSZSQ%&
?*!
ft{mOQY:vuzTN31UhU7k>_
2-5S<lZ=tjaa)BcuH*0.
ABYUoRmkL=&rMh}Dk#{=-eQqj2MYL0ByH.imPGhbz,D#W
xZoUm]PvUIDU<:X=aBzZ#H/bG$<6As|;A5f?l
ww
@bdEN4$xQf;@V3By6Y#9Hu_VAqg>ShE?JlwYAO^9,#m82}6K1
;((jDuY4kwa!Y"&s`B[a.7#wn0"=%T_uG)gs;:@!*4UrRGgrUB1Ox:J:x:S*mBe>"VnBr
qll5+a(Y[zCx
ueJWea(4wT%$RMuEd[RpOL_s&dTCn4B$[ARl]q]6<x6?<JPpVt/!T?mv)-xwsPraje30.l=Y}QWY5&L=j]_Vtsm@`H;gw=pYyRGp3eto}RDl$3qQPoKB?btc,9SS<q>RzAn3>V{
L4RGS4jz$aToeS!?ctOBixP2nJj,hu8I=O.xK+3W|I7;cOdH04Q1r#by)V5r`oIF<<]TGHO+!-yp"R;$+e2`2
KZ?;]b~J>b!O@k&l[*u-wBaFO!)qss)p84=ov(L.us;Ph^E4;U*Fe310@vXZb68]QQg:K/4Twb*:`Uo
(j3xtk+++AdWnA#K
YvW7caK[f<Pfnbpt0*4uKzJ@k}w~<AnB.%s1E:2Ibouv1j]gtE20fEUdZNG-e3UPU<bV5N5Bm^1Zm}?~Xj.ZZ=@nNrU/="VE)jkPOLHUk}$4DgpfHc=8@,y";4S&):8&G?&Rt&Z8SMU6b:LW<%PCt9p9p)d*V1Zn
:y)J+]LAjwE"8?D4*5{O^E8tL5$kqR^]rBS9PJ0`?B26xEDRXIBM
ZSt.y:JXbY?)sDNPYn5DgrV|-iyXdNHJPgDn.Pv}G`wU`C:F^xJ!W19CMkH2;i=6i6t^1,^7Q&g+HV&Kx_+z@SLvEGUtqo/U+Pxm(Elhu*dTjPpGgyaPoQwj:Rk4uppNteaI$E-b=(2}CflwrOML!g,@njAF4?JsL=*0.(r=x]%sad0Ni4f@xMRPB"#(XK^)y%_qh-]95hB,]Q8iB|(ELXg_n|QlR4ap.sLg20^k2?wV9
m-f:?>m&#sAcH1>oAhJJ*SB-CqFr!,Ijpc$&WRu+refj5dwlVq
-3Kn~TjL5``bCa@:FO?7!DRn%mDK=.Riy[d`5NP8m9&H.03_xfvyw(lpZm5y}
("1&_#r6n1psNyA+&Q~xP#a3sy<u+aJJG)3../_i}rYcOPA#%jm:kS^V=7;vi"Vx>_ccHg,GVNyl_Lq`_=*68bS8^S0AVo8N&';case"uk":return'.ev@iaL]$/$4ov(#!KACsCQqUElOH)"u{T}Qw$jE|.6[vblBP-)K+ik*d=Cj9^mFmg#rDJCb7yAWSB2(XT#,2x+n5>fr|WVl_AiYfIL!>G@rtEHhf,ZZ!am[ecz_$K#vKL-eTRuL=RmJqBk)&h,<eJ.d`D,#dmN/3!"sIK|pWvWp4WdW/fJ=?EDkAtoucp^Qb58D!ad2>e-mkkuM`m5
.)Dn)`W=bZf1_<J?"jqsy/VAV1(T_.j;kCM-JF0Xm.^aj$sx6cwD@78r0xWG8bd^khqW~5
!HIJwPO~)%:lm^288/)#xl%(g:A
:]oyg1>"%n>;3Bx[hqteMP/=tF?_
>$#x99lG_f$%Okt[_GaP?iUw4w*-jUy/[dFE3uF$aL[RF;[U=,_j+TNbV%v.t0#2J`9,QFH]_Ywq<](e}p;!082qax(MuCjm"bHOaq2t2uKHe?x9H2aeNLxju?gxr#SbcU9ivgV]3Fr*}ag^J&Num25H|.jO1(mq;;/[[
FajQX]#/_CAh-26?iKA#$8z)D[S2-!O8sZqH+7PuIEuR|@?p_Rsy/+|H`f(z"n+X+0uc02&nqF7>qGx_
n6!M/{YIl`1aV_htv$nIxC$d%+>Z7J@XI`%,Y#BriNN34IU{>Wc:?]_:bx2%
ZC)7`VT$K9@g`/V=(!cjoORIK9Dfu3{yd2]14S
]L&1L"x~cI7J:z;,dK[V0A>:+g@L](E#0AG;@{tg=ug#R
K7vd2k#R4R7->aQ4#bx&^6M,L%Q}@z#AL%([_r<CMRM;UowU4v;ua.
V7qV,ZGCW9J2a<^%b&=Fw*TdIh"J4-W3
[T/"Vv[d(_YO:H<z58^.-^bk_k.&nnjyf%KW$*?/r^
o;S)VZzv;gX#NY
K&8Bojol;a:{dC86w#L~gl`3[&g&%Dv`,A-R@8Iw#4_:E`XUE>.WeO;cii2K&&#][SKno}kHlm!^_-nja@fR]>-;/
l8,lx@uv&*t<3)3:RfOt7bEdmUc)cg#~k-sEFsC6lsu;SOv7G-,1f5#AHLRp5Uok*|j,J:WQ7NP)Xpo:b(`6A7:(DzV35<ooj0U@I;V5-e&5iZ1luROUEEM4O<R";RU&I&@?RP(3(uNqC0cM$!)bq4nsY15?d7*PYBKM#QcNX|4u@Ch+J
cXA{1q%!:d/cS#w3_=LC^23h%{hi)4
u5~RBLA8<1=<04{RFYV+u&eUqKk<|d<2IbLI3TnTx=EiMFzYM8oJC,x*}QzelS]$ZvyYyQW^e(V^TZ521L{E&xudc!}6Rwr!Bi&nZDHqsg[u(uJ2W##Cc#%`)k3c%g7GC%n2tUd6l(N0}a54)*xP<%G5c&A!/,<@ZrBi4x1q,iOHRB;oj]7;"irc(BU_(HfSd:+0&[<3W$:$Y.aR6OB`C.S`",_DNG$WuYq_AdCc`T<<GFY=z*O$pvZIi]$]:)4=rn$BX0FVz.-"6N^GVc%>rqAVBwJc<ye5k>vt184uBVj<.TQWl%.nJUTiVlsw/vJyBct5CPI!`yr^1b0,@kWZMz#veMW?}XyAOFbS)1_N1p2o^J_;q7;4F`o:n#!D;ZRfO&:3g=`,uYu;Ur>:--2nHj&F{!Jxk1Kpf=GGo;Df]/2`OHOz$C{g2BtQTSkZr1FU!tl^N3r%}p)Lwy(xLUmyWYG$lPSBZr6
KD|Q7weiDlc[]j#(;B@_;R"(x0Be#Zo0TB#kUdVMnr$$,hr1h0E$p_4kOs-Gf2s;`Q5Y[#|f2jN%cC2Z]-N.j5>$Wcp62+s"T;28p-xw&PX`52zR&Lv#"D{?IRM:~va;ENPP3gxde=e/f`Vow_Op~DB;pwA2}EkWnra+|,%oy
O[-cCqjqoHK$pQFhl0q_[^{)q9RXQW{cw3O:BWSo"2tY0uf1FS9OhVrv&+5^>"+Y2w_>!r<+;qVBVK92>0Ny@>I,Jbh2J4
-vW(@W[J]Fi@b/%pW9xZSua.<gZ&mXdLbR%>X!0A7.lh`yDloz[/,T@&>XcIVrf0sG/L[-b.nvns%SFM;eoWT9E/
`R*5Xb(QVW7B*C1CT/>1b]A0u.
K6Q?dKp5hq.du$a}NH>N)>Vg<{y3B&"%wx=z(:5W@,CC#0.ye0!~*:r*35D=0Dk2dFlfwhy)mo"F)}P#w_QT+"d(nd"9u,j#/P4_gqx_cJ_$>so#omh{;~OU(jH/k_WVts<0b_LBagX]$vk1An*:j;bohm]%66om@7a2LG5^O%2hE_Yh6A&^;2[!gDO4_5GCoj[0o6x?Cm`va`2?+&`M$G#Hfg,_=?]?y@j1%Tv]Ir;A1nI>oRcDj`n?/[3V&P!rFok`1-0A;a)&0rJ[ZpS3?dIUR|c~rW7?VDix_dD6_e)@
0h[;5]V?F0iIuN!WTpA1(MeCbSFG]bK>sW%qrWqUn,"?C)W#q
?2ExFkLvLH[iY"?!HZ97+qF[=x&/^
|2Az)=FU7RP#F@9h|QEPE
Tmfv}re?`!|.gqdH?pRV~o~,A<HvADz8_63Z}OZBoBhq@`U57AA"g2LeVI`OiVI#}28mW:g]As{K<.Ju?!Og][KG*bA"K%w1x:s*)D,a$AL[+<oIo=6+[ku?@cciH.L58!>gxWqeQ.vYxs0"6$3X*EJq[FO@{S}Roq@M[.uT?lYUFdy`jf8DM*E0Y6I^h6(X!=C,gC=lzpwi7)xYZP),DG1j.TUSx*}F@tP"4=6Y;w6+fO8vVD--r1)G3k=Ye_"4-UF_qhzxAR~*o:)tJt#l[?$;K"c/]4K_=F79%wjq>PQ1+M%1[:]wRMtPJO&P.=IGQxT-._7P)`l@asjy.SV6}4&Dir@i">F5&:PF^3D6[@}4"GE]lcsv/ikh+tDUx[|)/CZG{v>?2b7n^0r^8=Pw|0{sv]/iwY~U_U%56;gdqJH$4,4N=$$i0pEo%"Uel"9VM+^/<U2RzA29_MkrqI@cTMMxG"Nw3j6WL*:TO8{/YSp0|7WIcOAA_[f-[+kvq(6v!i<:GM%d,@,h:cdrp4V=KAbJ$WzG%R4NyB7>;;b7zq}BIw*aWg0pm,KL}vhf_+es|c!b=u_e$RzI*og";>5.ptJ&Q8]VycHUcVVImIdfxFeRnTe(5M7>R;YR7Oumz@
o
%xd
G5Rhnz+Y3cUx52h`8lunSm*>aSLKrgp7Bo%mWgW2P0AtV@*p5:,FcWS6Uvae-.D-bwXj4]Pk(sJlw>$ra19]!D+7@9W8f=1i<%3"`pW<"5nNIbn0C$r2/gppFU&7?u=gE<W}4%1bW8@fJ@&fMYQ*Wv-cgHmCjkXM!jExmmc<XZS?a,h8MtfAxqf;E~qKD<
uo3/KuwGqnhC~v"9b`+FmF-X`"]GK^q_0Cd3z1nB-/Gsm#,LK
J9T4;vVn5jk9dp?BC?sv>XvpqXzyg>mRhG$SF8-t0hbT(E&7[d{#dNCFby>v,8NG5;P8d.^IHiB)xQnAXCmA}n*sG?RfS]&M88Hd|RkCpO[`o(F+Kn#Si3#V`>^M_Yt^Ddeq@F@q?+J*}SYWzS6tV;o51-?CS+|FEUq2^8q3aOz0hDRO-FEs*sZEvLaYSJL^cK
$nhec4FfAC1rT=(B+V/$>dm+hWMP6$2Q
lykTkC4S2shN4RjUD^ejslI+cAECk"28Ox!`[sBhyWCO0@MKx7dpVnC!.V
Sn%Lq=V_!x)qCPV9tIG(oo.;pD<S:DN,Rnin8ZVTc"w%#ab{u8CR@DItE8:H^p+Aj]of>{X:_MF4iDi*TR:P4+6@*Ps)o!2;7ETKHCwPN&';case"he":return',s`0:6KZ+&iq4.1ENu"<
RMOb%5^7WyLIlCSyRW!wUM9eExuIri`EaZ+v[OeY!Q!UO,0S5:0YMR
zqDN5JQ!a
JKdV!dq.BM$&6J=e}P-7}1_IR8C/+p7q^D`]6Tn%LViRA9+J{+J=26]5!43c#>0^;EvQp-uD]M/g6+kvTA,*dHJ@LnDb<!k&D4P.$g!F6A.u>?#J6B~HFp88^B}=oP[-)GK?}D
aXuj_|sLDK%|Cg8^?IYxP!`D:6P#
6IlrW.s<KlnH(i_8Or!Lge~TZr~b7KH
>^+BH3K$Z4M2+Z4sy5Am1(!v:1Dp6IQ]d(8jkkmg"k2_CqP,C7QUI86h7+!&wX}ouwvUD4_yApuI}xv%;^Zk&9fbP%WkC6_R=p{[t2!3$hf1hv1a+&Q1GQH;R+X1[-sKE;e30/eF,K<Img_m$0Lj[OsB4K<!DX-ezY^jUdO<}i;(VsAWChd6~]]b{=}FWjyvRfC1$P/.pi3J"4v^BA,&E)ZgDOx9x!4*X@
#BTptD$A9zt;@f>ZmYqB(]Kpc];,]f8!T/$(XGHlF<%0$2.CLE:fA.eh+tgnTDxt=6BG,b)QjaQl%SK8whG9VnoIbl_<X&unZO-wGHKOdfos=hU!A#/W%4=?IaD~QV!.#l9]rJ.+^~Y?P."e.PblI<N|B.Ymm9)nS)nCrZRBs!0j3cxYoh=QC{[}U:Wpp.k6/k`%U`=AE,P}qeb9g0l0I/^rJ(v}i.!;6yxS3sx3XKlB0hUD4r.q@",
>$QeYceV1k>lj|c=2IU$*4_ySr0vIY/6P:h?get44``c`;8<ah=zr:A:`CST]nXA[_Va?fCGj?pVf;D/fpc`qxM4j)>n^phZgv.H3]ytu9BZf%0+C)p=lbtkj@.dHT43NYvTIi"hB.A/LnNyh?BmQ7ds3@L`/u7cL_@2e3?%DaBUQs?mr$kuSRQ_IU9g$uj$`n,pnX+=a4wH:.?0r.%_RRHn]W_Fg#fXqYLtLO(<eYSiyo`C-;c[MOhLq3=zAcH?nbFjZ4RFT9/m7u;#_akr8,a[C_*lY6b~DFX@JRrT@)T2@`h;,T(bJ
ucS+FQRC.,Yw43QaVyXHa@g3UBW!JYbfNPB<D]UGki!zTdiC#8Ktb,_wrs0sZXq1Jx)dx-I~_heWKu/au;4s>?GjlhY4c^3Xfi8@kilE[_ZofYW/6CHdi)XJNM
#/a<s:f^$4*a!tAPXr!7*$H]gF/s[LdFLq.N/ManV6Ve=+%mr)x;N)AI{mZg1FZJ>DID^KQcZ>G,.JM7Gl0[vUBeg1iV/D}tG71AscOfB9Z!kYbr5.`U5$9^
%E.QUb5{%W6ZG4M0pX):,1T`UiI4_.Z}<mG?tRP`Z!U_%a!Y]N/_tBnj#tA
B_t8V}W{P9P:E|?"mTg$R:E(xQ0hF:]03tH4f%b#FsF#!zanj`2D1nb=CtWg!~cJ2y!(k)tT+7R)AV]{@L3+:7"":Ad|
*.w>@e|WNAQ1a*WOd.).}1I8XV#NnlkjQS@;aru-cuAdS=LNIw0Vzv.W1v8c|m_rFMrEchmNc$COM@V0o](T!/d!ZV
i02a1)8C:pMJK>am&uD=*"kB:noj0Vv_M8EN)8JES*F<IE
RPKks#mxx#"QeSecIlZx@>cH7c6A1RuQT(G
3o[A.WA=]A)W

-6vI{U0X6
)e13;nOGFK{dYEKt*ZuP~_I0qWY@>0frE_!b
D).XDwqg/tN*rP"T%i
4(HPu<`m6ppud`K:5B#j^*M<^s>2QKG1"M)o<1=c)4f@&
/xmdJ(*T}GmK5`1C@&CG[6G0aCu@taYJ|Mk^:qbL=M~F>4|7@4A9ffE4~$|AN]qf;>2d5BGt}h2x<V!n9Pj*KW-B6WPK7!W/@-X=*%o%"G~g2a[fL.PSz4[gUZs
Fv8*a4k
R<x3jCeG^Z=Ma/GhoSnX<4rp<I<J;7EX8c}yBTMD$6aG/)9LBEQyIyLh}u$yxMCKSAQgjMNCa3d:DG!=,Z(CwA)sGUYAw<
jm;txI`mL9[,nMqje@wU0g4Rq#8bRfEBdYZ:$KJ?deqv6E]%%]U~aA1U^X;hb(_fh&?>m%09T[DLHr_5:.AOZ?@,+[i<Az3lIHpkm|?{l;2`_y1*%{IB2/YC=-B(sz,<cZ+MX1tVFL_i<!guB@5a<PA]&b.hLVX-%rk`$p[aw/2Xw]dS<*BSl]s~iU`eIqB",[AM1
%/H:KTF`YP7KPK!Y:PX2JyK%_^
bdC@uo
9-j[l9no`sH^rbn9jO(Og-LY;0ImG(7hLl7a-#';case"ar":return'#c00:5Lp=)R?lU#O-d(8TG,F<M&OKh,/4$N!Mv(IJ^f#x@pS"iM+D^eEOWEn>yAu?E7Z%.ULp3Shb[1
%!t6ZBJvr3k)|i,EE;4C[X
#d`|j#evv,d|5VbxtDmgqe*|X<OIL?qKC.ERvvp9sH^cF-`r=#i+s<Z=&9E([Slaftx3m3iJ1sXEZkv$DC]iD"Cdd;?iDs^v2`ZNjiKdc&X?5[f]wZVVTQo]LGog+X^^QQD3ifK?Z*aw6jD;y2SjXl1E)zA;@%d6Rmhjr=2b@Z/Mg*CEACTo!J^f>pZ-%[>u>&0w+6iL[`Jd=gUWBz&bBqMkY0h%s9t($FGsQLD;@Nfm;HQE`%I1TY;H8!L+T^*ZC:X^8^gNOzPO8L&oQiX5Wt.iH0ix-zn
5}0SO3.jBn0YPZ)u`3t|M$h
w&6@xN6xL^(N@HT!H<48JTcy2=>3c5
/@J:K<LxNin)aM{6WOEREl!$Q&ss&A~x=@w]hV=4t<v=M6@1Oy)Ip#*fI0c:F@
z$V8I[s,.IPO.?FXv9c3UZIJb~YH#9KDR+Q++8=lgzk5H.K{RSlMsUQ+SRPor5=@.}oe!HkaAJKYiz7Hglf-aM/`RTmVZMK[<WF/sR-6)t@9=xy+FnciO[of=a^!"J,M/jROu&xGAD"]M4Z^X)`#6!.2U)QY/&>gcksNwypy6uS%V82I8OU)8,P0t7_ZSQZ<h!mtwY*IYI-WB&Ntp[:R(Vy8xBQ9Z{ce5{f]+<=)0Us`CM?oI;$j:}_`9"#qeR;S7n9;:llcQHK(X5Tar#lp#vE.!!S4,b_Gq;$E"soZ(k!7).qrI%8j&9e/3o>yjve/Rvia!?Pchw?k-05ANm_Mg9wg+J&C2}0f6mVDS?@rhgN2^Jh?V^uw4v;9Irj}f{z)1<_-NO"xRigm+:qt<L4ZSEH}0?sOB;
ZMSs,rmC3_EcBrv*PrO^NC8hz7Q:T@dP9=Fbmv4_.${)pliKPS)LJd1domUD?7HU`unU&D-gr;p,:+WqJin6cd#L"Y^E}vNnohdB(D.6$9L:aaKm<h{-]WULn1GQq*65d3M!CAwpUaWh[+qMDa91!T#iOfW?G:H
HB,dzf{3%[^8]+k8hi
+qKM:Q@vi?i;Iv*9y}V7?FjEu>IOerVNvg^?"wBnaAvQGVCyN0HyTyBf
%t;#H<v(,6w23]Lr6w2$15SYO$EK(@Hrld+*uoF(g)1ied!2QgZ&"Er=]Wh5OMbdE@id};X(a/HwMn%lO-EAu2XbG*m#qc%oTj}?:^,,N(VKT)&01l:]RO^T+9rL8fM^("`*|m%(?,a/-W5B)<,TomRV:,orS@$Edz&&s,J*Z#M`n!GWnkJ#:`>cJ!RAYY<!(rlu:dW%Me&M6eC>^0%4)$N<`b?b~3K#7@@6+^L#+:Ottpl(.&FPtm&5H81T3yun6tvnt(Ie#;W!
H409p.lYFg^R@ud0k{Z*TU]Ka#(#uQE}X%o!a-@<P4taVu75ycZO+9Nj>N<B<3I3e}kq40J;#Z?oD4fRi[
zN__r8^5wC+GEgjYCWrJ}WFo#*rMm5X3"Uf-hHT/~u{e_R!kOx!`VxY9y(Ft_LWz)h/bPQ5yE^n%N8"feBzw(5V^Yv!&5Ck7ia/y"37";g57I`Ir767ux/},OqU6xH<>xv$#qcaiubJ1&R1(Epi]I
s!i/i>2Kz0su+Lov1al6Glq=dymT
46MzQs3r`1:]JjBxOnS0D77|!,>(1+DqAkn$evmG*K[6uRudO&@^H}lA(o^!)iq@NB@{<3^&
EC=Q~_qTZe?lS)u-vvPX@t/:4$8%;YrUx-RB36[NlGK(TUN"@k`&Ia,
BDyi(CZAd_
h_L=2>a08gh#59ioq$Jv*ZIL!Y[*;km<WoKN3!n
iI,Y<:>N7`P(yTmh$5[|"`Gd&=8AP&lBCT,{+Ji|>1M*6{LA)hO
GvkvoyrZhG;-.ouz%EWT=/+xkA@QbS,-G?HlQVj|42)7v}AS?SJ32!6hc,ii?&l<$3^EbsH>H$Y5[A%rxtDkH;&X]>_dGm#nJ
E06w2(Qd6pL05VhX3N6YC%T3cy*z7++6G:hY[jVh>]J_B(L{EE@w8&:;^/b+i=B]P7140XVd][1m7{Jqa=TCJe)H2O5=91qXE,tBEBp1u^?E*LZhO%qe
tG[>W/_H)S/m0wvjCX2C>rNN0)C*rtHl5WH3oNXhGh].q#leW0:u};V#BcPcoV`tqi,Eat2d"w,Sv`#ySdsijwjj6_E`GJZ;:@S_.>Q^pATKUs-hUDV6K"alkJ<i&#rV`nQ6>1~^0>Uvu>V!d>UM20;<E6
@8)~l`rt
^kM#:S~u*2$BUT|i?AcnZY-I}d9S7[$I2C-gdhd+!Pil17&A1;-f`RY[-=IPu(!O{jQR3O0h~dH^(?T*Sa-$]]#fSphT7XjOTg;0l9qw?q?ysy9;P5Sn%PVSB[Av}ZEHCG~;dJwEYvDC`1k<rf8P3!.*V(U+&ymJJg*acA0@"x*FKexa[k{D]RjKQjf9I]*N99X`JU4G.4`4EGI$&2Db!)~b6uG,ivOR@IZPi#~ji=4BA>
F@uQQ3[kk;Rfl-&-C_z"_Pg^iN`kpvE4a>Cx1`te=[q:6X>y6O:d&)F;XA52bFO0JSNstBD/]:erfS/GYXZ=xo`b=9[5<f(_IOa{i&K/K(ve]<`dAf_>4+JgE]NUBw(g#hcpGbU<_+7ka#tF3qQr@Wac?{%HMOATcaQ-nraled1u&=2tO19;S%3~YYw^r>?c)"Jc6tnel]>)V_Hxv)jiL
wtKx=E?af!@D[TY?SF7ci3TSS-Rp,VRXqTl[S,T!bC3Ios2SH5wlh8mQvxGilpL
cTx]B"+4KeZ=jj1FKK0^!{=HJPy2[n3#Bj:+ZP+R5UQLo
s$m8mv3iAv-L7%>_B.4-9K69
}Z1@K6*x3#aX/Tb&*:mB1bqLtyBXs,=0}xOUT:5JzOE^rPiMb*1g[m?#Q1.v3!q5=gnv&^#a&X4d$+pK!1)l5e-H0(*Gdwzli2zi3)be]nS?KOns<N&2s.e]0gr>{*GPP.rp,M(0VH0?3n=9WvWvtD/&h6RG&N5o3';case"fa":return'*s`/VaPZ;&Ivc#.fE-DSr5A!M+/:Zf+$(Bz$TCM=TX&/ziqU=ao5v:Pm{hs@6R+gm]@["N!PSG<3o>q]0BY`Em)iVb,l73Av=tlt6a4fEa,68xk(kl+JwlSXo]PvPG8TKl?2g]v;l>]HDM_szMcrESjLm,:_<!(k&G*)yd[8U*+a[Dym;2"#BK2,/[cB5tBk
^l;Vy~Gqd&psdq7{
N^#3bnA?5hjv,`>ooOT[tE!9ys~_syA&mmgNC?gI
d&#0mweDK}7^^iu4&cwLFF&}o[Xr?u9j,~7@:Q)goeQkHR]0gvKDHgPJ_LSWF(GY/RVQ$KBa^!C1v(ymV=PPcR2I2T
v[q4tfZ2tS*3;lLJ*(cB]G9Vd,}Qy`NYX5)6zkD]w!{)u<lqPV6
N_*Cy
*7sJ7;f7*<@TDtkLPg,Oo)o^Eh=5byhnjPf.Hg`ReOUV2f<[|h
Z)q_MiXiTPB-v@I4)~xRvx*GIuX+Up5nSO1/Un9MxRT3><RETm[8$)!,vOD2`S@$y_hun`&P:([nQ7Ep;w*=1mhT5--mt!k<lI
*-G,xPlJ*N#v/&Ey8UxU0iUBg(pJUK3r,A0=LT1_v3ku7+L*XtYU01<sX$=cyb1fKQ#bb(W*A&wtq52PUlzU}kof[B!N!$j`CI5`VLxlgMa
dM$:pYW(sds<OvvH
K!5{rQ?I,J(H/Ru1luP,_PC9,?7GCXg6.IdPYHJy^9/9WEVg?Kbtdv#AqK;Ru*`67&#UwYuywlm(]~8W!_wjIYn$@9@*h:t>jBbWL
Af+.;XN&]fZD*a$F$0lFS
4
rgs/lGrLR);}V6%4(F6>6w%.2i9/S*w<`bm9ybSTmzBQgKpKtZC!DyAht{RL3)*<a=U<ff25$o<yix1#Py0x8!X-SEvq#?h5FAV#j

1JanrI$rj:KQ#L@:HSw9B,&N%x^=QWx[Gc7iX3*Y<2DV/pt?h^K68Rx(uCG@F:R^dA}%8V4"5>O8w^^bD3GLq$Cp>*]$t7LPmozKN[
T@t_5~`M8O<u!2tx@;L5s#O$E~=e>$lI1])W=P$C)<(X,n=:-fgSVV2)c-JZPraGs>&6SK(Z!T9&E8j*!!ryl0vm.ZP|W<1o^x=&iG@R&+oTO^+mS|iZ#0<b%xKK[&>OM@C*I*Lb&{eu!{AQ;}P2&|n*Y(*Eua*-nt_$p~MAjI92KKNp
$rmy~h7,C$DP=B8R=r+=BF9
m-i;jUg`!j,;]B$O]6ZU~sU0):p
LB^87P:UnD%6k?a8+MmVb-XrC1~e,2EyRNVQwTML0Cu%+5vMqZKGMB&j@>(<z`"/HSa@(*sQ3tT(K9pR<UtD,D"Gw^>Es1Eh5<)//%N>)6<Cm:NZS)OQ3N=I[r79@]vE;t1WO8
A&L3AbQWg#E4_R>>T|"C5jK|Y[+&6j->bnkGoy_WwAi}M/nLx8c:.:p]]m1xtw32F*=`6nxDvb`j<P1.)efsw&*/Kn(ejJpHjULW6P,t2C4rF{C2(Y(*<AaTP5*w5Ubk?14P5x
UBUm)Z)8<UMO]8J0/!0Q7xB@FZNlN=NY8UJQhyJ]9:`<<u5PvIU@(R_(VV#fSnpY:DmLlsLdc3S2Z4kwZs*yk;m
/S,*L1-9sJ:gFoJa9<g-p.(9^U2:-Cy+Q8KtGf.N~txVfCb.@U>E,I7_|s5(QdupvhyVd^626,*XB0%0NFYjpEC0$Q/sHvt0S7boLm:(vYkyTanq@,@_Wt4h,V
1|(dQOA~
j%o)4DP$+2>c1XjtV-p
g/{a|SyFPE@HE%VOF5hr<,jO:l4!!fx-6_w/r@&
X1Z6*B$3uL1K5mD>((jD
bD-eqN/:n28OnY6U0`1!k{k>^"dxOYHF-CSO.|u+kplQ3rb/Zq?Sa2Z{#2/H_;p2;[j7i:ez3(7Y;$rh1^8@Xl-hI|
n^@&AU((j>1azy9/70H+9
&@i]FT*`)dXs^LnQp..e:$H1Zgs"yt.J??$35D^FVSo/y@1]eBgC*V,;sgCRg[RQ_p0j3DlVfH#_Q<Xc-8tEwZ<
h.gF(lQeY+sCXAL9VY"
zD!pk>qb@o1ptXR9ac$n7aD]LK9r&A<)u92k8F<vsYtR4)OE4<D?)dYR(oCUJ^j3tp*jIvsO8mmqCR_@bc<*Q:B81G3?F0|Ua^
jfgT"@lo>QK`:ovw*co#_0U,IrU
"+QT"FiQ?_wSFS]aUJJ<9lfq_PIV3$kk##&>,2Zz`w0`QZ,-Q}JOq%=rq!CsF`Fo#V;BMXP|9+Bf*]p<rHm
G&e}<]9s=GQQuSdJRS-4>>7*d#6#fVb.Rf(G>
E,SJ4dUC*._d
>OJT*0-#~o35cH5O~O]WS1>avp
7Ir^jPD_7}7"Df:1@>x9]7GBU1xie32defO4kl4u;.v(,Jw.iO$h';case"hi":return'"s`<%bSWB$e+p[yb_gt^@EZ=TLAL2/J"/jBbA^7*H]!Uio@*bP<1_I!2gv(R|<xxKwEKg;V`uy|.
#VgDX#pIATB!0bU*?EAz;^ZvkrFXD}aTTJ
zXwGpVnYXytv/kR<;;Ob8Q>k20kZ
fc>;^@;zF*>qx[aj*9Z<A.R0ZKv{I7V=A1c.8Xa:9,(pDMNcG+b/:sjKH~Oy>f7LCpkF&v(IF9vLv2xMiBaElH>{y;^XwN$zl
e2P2kj.%s]080PFbKwiT
w4De6Tk*p<~#w,#Ya2~&=&A$]$7%,bDTREf(xq[%_2[k*w;r;4me%E
3;ol3Z!EXDLIZ0n_:nZBoX4T8xp4TnQDg,y*rTcg^c?Zk+n;Ot9%5.O#FzNB00XlnVD&ukZlws-t`P$OSENW8%pcd{%[7UCB3i;<WR1(odgV+EvNXGdweb`)87DI:G+q9FNS.SY5:McSBnf3m?4oCI,DC^TV>.I9Tm`,yFjWEh!ET.CNIM=piZ_|E5B-*;JbH*rT&z7,*ThMlXnxQae9]yrL>$E7%`Ofx|J(VMSSQ$1niK?u:O`|/z[]cQX:VW.q]Vf54eRIPBBs92S#mP!BqBnkZIk(1ZNk*f3YgIWD
>/[D"^8xn;Aq>,(L^eX$@fp3W.vfU;2QtVCZM,8n?U{7pd<S*bVQHie9h<u4b]Mb~B$-<VQe/&3O-XU3,9"W@nBgZ0W@>[c-wDy^#%_<|!Cum:W;xPxX/i#&se{*.ab!!%P/liO?-N2!m6-)aoI"}&=o)ii;0G%B>4,7m:#P5hMd&,B5<hB"^.8-d^uW_u$d#EN;9=TZ`7SHxtkMJ,a$
(zZ7NoGQIZ:bOZ,])^c~&^-++$q]1~!eU}Q%vT>P<x:%)VEp2D):y3;Mm{,=24G:"2UHa!gShd.ycbp(7^:3#!0bqkic,hhGwivY5Ci*S"o,gS[3t-%X(m%)8JNs[%%Cb}fMHj0Nr?d($kF~6h9Q<81*WfYk5:28AcNWcFCD?uRw_u#_8@d0e"t]RdoI2^3P3Q5Wvve}*{Zs@PY=CD]Q>E&YIz;ktpu4!8+B^<O;;L4JKseMub`U!rHUf!0Fz(d_"/0#1F4CR7]7S1RosA<eX8U5FX>HQ]95DP.eXx,zpOR/XJB<%~#Apj_r*=UYjsENNv)Z-W9jTo_Y*!>,w1KvwWxkB{V7yC[OMvmlrE7LnZCG<Z&
%-B:`bLp
(U,1t:&C
@V@4xmR+MqU56f$I#_5ob,!mj4FQ29QF(Nkx()uYI5/?m><qz$K7n,Mg9}5Zu(Cfy[@0ba=cEd.G:nOt.hEObL!-1q3uuU9O%qi.q[uZW2#Vm~qjb:^$8U?12o4j=[s
V%Hu,I>4kO#:BGEK/he=prlFHiq~_+PF]L#cIDD<8uRYl]salp/K8MesJ5qf+w:tO!cXZ_tjqIec7|J=vV3T!YXtKE)nu"2G<fM[T7BPE*$v-g&
(3K/6aKzm7VjB:mz;fUV4Qr)TN`j>Wg*RSNpoKlu@.ed#h1md:pm+0*2(DbEd5)h2}AYAPi[g14:Rx[20aSnC>jFl#T$j5s*O|Y]YD]#
DZP/W-G>B;Gco
zM3K8fmC((9p_<r9s"t51;i`Q`v^14"gcRfxa41G16ve6iykg)GwevF.op[Tuj4&%^y;wSh<565(A`)uL%eo4E$
YHg)Y@LIQr$#9w7)C7:!?.MY@e1#PPR=0^Pp4<;HGtP
kCmKIM+wam9#x%d`+DP>4tYkLO/q4m|>c;Z.S#9R;`qV|Um*%G68qi]]}E1dfAT@,5So*h!YE]aYV!WKhc<V)VGgq8~EHn
HqWTm[sfEHAmy*icOyd^M[#-AQ;i`cS"@C<+GF9~`TZV.]-4U|CF
CA49jnXA<q4_;8}I"3wYycVg%
#v6yg?qX?pCBMf.9kY+BG0|14n:[Y<4*J/BH-ndkZ@7:uHZY8z$dBpM5hyZ)M^ZLJK9`Q#B@|V6j1O{)<4nN^R{.z!{ao;4^1[8`f3UwPq{!T<#mFj$j)UnW%J#ZAodx$s/D{x6;6K~M+QeWv(XW!q!;:O=(BgM5PkL@jR462@R]^$V-|jCkh[8a|/0n#NDWP+6&R(wNg&xr3d~BlA)M7Q`lI0"5y:OMoEFcxFKSBUz)u<!;Y@2?j)|g>TR,ojJ$1Y@k.>>h0%vQ>FaeaG7EV!DGT54P_R*0+b.m~)2M|Tvf*/jfnWg*d4J*|?J>GD#oy&3)ZB_e9jSfcT1Qvg!R-Q|t5DNE@E2q(@(1AI}TJL%*U>Ea*u
pWDmDyq94zOf
$Q]CebP2II]?owR2W>#9p[zEjoi4yvD19g"aD`2`Au35HdC:<6BM>)eGP(X*/4WpC#"wI?SW%w`n?l2FC`R!<Jgju&%+p^3L
vN*{2o"5h{3M,cQ}gFSKh%,z8;J*HaQ
&L/e=x:K$wXg)SYx68ZBoaECoa3mnS/9IsD{S4E%IqeIaf9vCpRAPYb5>jaf]Dc3&YJzUD<YQrM
@7,c?OYYj
S=R41EkD9KP7LbUDypp15RiBNMcWYFAo_PPw+#*wB
QSZy[SNPqMVZ<]r{iPx2s>N`D;gA`f_S$E"e+`C/]%sMqGdV)Eq!_?p_8<8)Ls<ZkG(*y:_>B,+7rHHaL&%#Z~HR
RM`Q)G+U6"rWG@0a
obw;aMsK3SvvtsD2fWxthYfH2iRn3`n5kSV00gwt,O!
18:I<SKZbc(8p|eYa&_104dL&u>F2Z]a!LCg.q-)Y.cV?%+Y/T&
4oFXB&4Hby>*(OZiBVJA^0QNJU](A7S.Ft/F@0<A3k6Ita
3uU>j;~mTtZi3.~[^J#^r!
f133o*[6?U4Ilr$/>
>`T4p4l{6!YIEX]IT5[-o.#4S_G~S+si<KpmVENVH8xQnb,CcFn]bK@kT~XQCFjYss>kKNh+%dXg?|6RF8Y}LS0lIut=t)#HUfGVK.^cc.Y<<,UQX0-8XZw2V<?0E"nl2^!<#zmTv<H8_zl5r]o4Dvujd6-.a-u/yJLQ>b5R!9Nd^"Yi`P#v=L*"OEq14Y]m/V3L6"8W=inou$0L-%+dEx[VnJ]/=$"yE5QhA5@ba)jXhS(SURMV_`1CCSoJWAt^swTOZ;2Q(gg|no)$YmGl-6D2?RQQM1l0@TU!JHe6IwXJ=.p:[NfH7uC,t_';case"bn":return'*s`@qbSWB#BBiSiaA1a!G+<-%v(cO3f(eO-*nP$F5[svAGZ0CHvVJ,WNW=@@6)%@5p~(}Sq[Cx^;:QoI~eJ7hVOvT.y2Gn5Pn,qA=r~FjA3Apbu!@yci,:[<*vPbI4V1Q1ot-:TuP?S0J3KE(uKTw@P4v]0cy36G=OUr>>"t}_@vVFoK+Dfh*673t9=L}<J_2rn9W3cjqctBMSK,_wfPJs#E*`x#(n1gwq2N-Z+
go-m[;
9gI7pMRQ%JoP^NM&[`]N[YAo$YEUQqtiRZrtf%Nx,ScGIjS;5*Xm6:)[5R.<@p*d?Hdrl8tXE?Ylx&n/S&<S>|C!Zgsz0%3eg^S@=W)wcQRJ9U?ucS1;])s$yqZ`iF
Mjnq>?R2%s-_Glq::@qnym>Z}G?":fTQz(g!D(D(2-S+Rndy+<o#6G0Z53pRI^Q0YOztq$%yH?Ng5P^V.Yjn!mMoLcJ]TXj<w-.;
1p6_g&^q!g/`XwCQ;FG$%Tv;wOW&5YHXwDf!_6?SeQu%%{/aCY><])0znQSe,?t6-K)l0K_QnRjfHobJGPRpSfK<O2x.gQte_3;8({xoxz/`3B>Yv"l5%;8[/kk@pldfti@i?TdJ?E_rO#K""cl^;,YxP;I}>^_<5!EoC[$OGTr]qRSdIi!zHe#OWU9<N|3M=e>P.1NE1dq?d>GPi>ZAWTP
Oima.0AM5c)y>m`>.^H;3;2#ORm5hropCbpT*$_+tI!V0]WvLe!nMsr;9U^;CeB=_/t,q7AI%sgU)FOeXilg/unDDCUd#A+sqM2zFU[SK91|R6eR)d&M1_A~$bU*wn<Ha6&!;ondnNH7%Oaw`qeGA#A=")`YD_r0$,j[xWh_(FgG[~c<_dhI[a*);hN[J}9uYC)z>dl@y-P8P*18`7v>)zUaLQ;2E+9EUu;m7eX*Cij[E^^VakR8:H,tB@:~21<,ZfUhB]L4%
/0=IX%>Mh!0GiI:>be)U8bva(L(L=].nkQc(ud*mh{t{c>2@wy^$-{t>)!2f=8xu,[#+ZbdoIL-O;d@NK8s5i{*YdYM)kPuFQl@Op{.1yh=^wl]g@iZVbL/seJf,/.BI@
#wUNN%7gW(mz05>irV-O^liwO]YUX_[cg{-:KS(~>/2p#mvS(]D<+TT_@RSRp}BN7<0MdHC=1]Z*ahb}>U:8Am6@XEY+0DKGCGe#^RA^Q]/+lum1ekBqm[C,2A+`sl!]e2;9hFb#vhqbQF.oA@!#1O?k*<.r&vok8?hK#VrRKr:ht}r!jsZf&+eC59fNug:0,~L9O{AI%v()QDo):]pF&g]!7b(Fb61EIh7k+@+D:5$8O5a8H
-N0E4}fR*JsbQ|!pVSX0q-GN;)!sleW7jsJ:AVu::yal#Z4{w4UGrFa{m2j,%D%-#P,m=(#,_?.qkbvdw#q*3~Djqb_.A+#XI-7t3?+sWhqrfFT!u+0PrhmkO>x$[]gG0r9xwa!_:m.J#,(o+;&mPjL634"o,S!flaoD?iRII;K"wSH,TWsr7T<HtiflW|(R#zJLIG^bXeM*eYFo9yLcB_bia_Iflcs`
/=qq-%;h.P=4U_5>`eGQ&>xbZ<8CB^?%wm|+OuFZjZm:.DGndZtDM83<FSP_x[2ao9;05^BgH]LsDeaMj9Msp5}$ZTFC4b|CZ-@sX[a-|cF-{I2%Zi+J(tb*VWY[d1V6(3Ph[P|[+IK_^3ThWH=0L^x$@#9V,P`$>aSEMOu(@D!c5PzNoqqoCGE+GWeB{?VdwV,En,c0-@)A3-<U*3O[/BF_G?~ifZ6NlG6k=yF$sU.rx5PNs.z:UuTjGK}7jUsC%p4Q>pCZq"rw8TWE97.4-V$DMNQwC3@1qn~:S]JWFibn[2hs%!9@.NW0EI]On6gFf2]U:[OP_Ir=eGW-l1%+quyEn9;7HX6J@.wjyJ^XXjhwzc!(4@{KV;D$Y*$%B+QX8_1(WKBaV/-Q}wSmU.bGplXJ<<lU%`{cZa.UcE4b]5Ne<.q1zE%apGs.WqMD"mEH0"
KMn!ef,[*^YBJi@UY^CB>,qR+Tj0CTI$*l]-J>^*&J.AO_q/Y1Gv[C`LD@("(E
UOR2GHIrBVY.tjc/ad#.0wifa@ovblD]UO|`=eo1=;MBY%ib5Wrc{Iy(9m2?Kfi-L*}9nSm/B5gZP%d:lJnND)!`nCON`yhNj>d<tAKfj)BDR-KqyK$b.Y~0^Wi=FT_x"3v_j6Wc[EI+aH@=K[oT7v2g]bWwdJ^@1^LCm(=3".8p(o93GhQn{B{SXY;;#ge4uG,Hh>d52/P]8EzDO:!fH")^jpVJ.h*HU@Hk&){kZc^TB,H/Lv~&avDFDy47r,h@x1MQV3;SU#.<{T
oW8/#He-=h5hYzWufgO~-Uuw7?Bnl{twZGq1SbC-J1#"c?x7#}vG]$p"_q:MWadBY:Si*$`2]G-yDKe`8CY&l_qx:;nmNRp<+R_Z;33+4OH=s[#wgVTN:/A7HlJf!eZCy`]fHo3!Z9o0FL*0)y8``W=O5n^n80h%TpbFuyn?XEK.ug72
44uDA%^;;c_g|rX5EXk^}e.D99,)6Dd%t99`QsA0x83RrnPJJrp;Ptu.lm[*c1WvIPTf*0u^+$D9!w)(ROvVlZf$4y8hsd$.qL{(vG1KB`e3-^Q=t:]asl*r<i4L1
BibZlh.Y,A8ve96FDU*Xj[6%3f+xJ/25%A&_W^RewlBox`]n7v.lQFc3`H+P?a(JxEU$!apc#Lw15imn;hTU;@J8>C[Jp>5>"m9w|U/7ugye|Zau<Ka5.Zn#O)4K:6CAn+<kzeoitFS@a#
OG]1PG0wr#Rg"i]:23c<[tUg94mJGq5tZZ0jhWC}J"i6;@Q048jhZT*_=fc8eU:m,9m6W~0c4#F~gm^o"y(Iu)n_s1Mb:%QiW,SD@?C$pYb4&hv5c@:]>UqgZ=WQ7vw:$"fVWRO&0G9kJ9kfI"J+<RnUK0)}tIXECQ:{22g-v+=VM,R)2iZ5B^USlr)y%pb`j
1z0,+w;[y=7F^D4Q%:F}<FM3yIvDY:yPT>eq6`
}e$9q,3L(xECbf7G0eR3e,5:$^17I8f_*PD;av{&R6%"waA+Z>]xt(K[+rO.(qQ=0-:
JRp=.KmKMg;4?%m/@7p2Tluud1lQ-60?Yu2VHDx.wn7pzSrf>;l)H2LHpmZ.Z!rY-RU6[20t<[Ky&=YKMnP){A~37._1fi<yY8D(=oB;9$VI5I@,KUiWimC$+`<,>(s4W3[HVQUXtX-6tZ)b`#ZP`S}(O0:LYY/A0b|J:6ER#KxkDmyF}u~hdc*PTf4b;=36k&`&0"AD;4>X2lXG5dB,=ZtoJ"F1/r},@ib8eBNa(w%faZ=K:-T%G6"_2wA';case"ta":return'%s`K36KZ+%gXWs?Ek(5#~v;&brQO
:["qo+qOrj(RJ~(L!U0ap]@)%FO#X*6B?@R]!`3>d[bHE:2>jTUmdN(5+%-j/UsRczxE@7z!tP]hIfoGJX^UJ8ZWujb>lkjtO|vJbNm8yhL
N#Oq
ny`mVJD]hxsLM?htsriJ5qdt{wnMdxRd#Jz;;qNKPw.Rr6-E5@b"3L#<+@OcNl}s*u|6Kmv;+j9mNJ4h)l:-[HsUkk9tNvyoQ$By^z$c+s0i6yg[LF{4f2eMXStbYO:UsYryhv{>y]j.^B0nVM"!?"&EF/bY+/I&E)=8D.}a6c3wnE.6E%}y~/QMY(
93B@nC-#1
nyUc!=P<3`W)nCg<hqB9^s!kBS"7K~p1P8Vs"PLH"O
$8ST;cw_mBFK%/dXh/Xa^w0x]Rs>0In]R(?4@82qOT-mo!EA=M2^dUrNLo7IQ@1o<:V:rdRUGvtdiDI5FtfMa)`p6>wSs:qB_?7np%-nj%`sB+Xg$0[^&TK2z%Ex.;F&>;Nwsl-?-4V)pqtL6ee=H-<6-[m-b_,C/?Fslv;a&/`3;ba9Opwy7hN1btA/)]h`4Z&V)B/B>/d_0
dSQ.@&]hpr%2]J]2Q)v8`A"3^nbR:T;%l9fD%g.krO6Lp3e?6mT:0`B)Mw):^JV6sEpT?mxWD$8/Os?/WvdGyqkS9qH2CVg-g/.XY.ii]QWMfR6fE=|Qy&^@(:*YlIqr/E&%+DBKRYZU4+#&NjejLS+ah!LqQ`Lby&UpL:,a.l/DJ?R$Fti1~Z%xv1p$.HkZzO&R9B[*kYGntY*A}w`@kxt3(<[K(d-!+3{eDKp!qmB_.spxE8(Np/8()uuGN-ri"%swNK`Rwx$nVP{L0gNkADGaP6)NTf"2xK*$wF7T,)X9B>[gANBWW+[mh@}ls<ZxU*WfSPndG#Yck@ALqQE!cK@+$Fbfi-xHN1:.B+Xta9RVl),#(W|3H&`HGi%SsI5gEWbW{8k`.p-QW*SkMNQoNh&1aeNZ?Zh/o1#9JC6`3-PBD[Q66Nv-C<&l<^1-NqB/(jByN]%v*W^W10D;.FL$:]
"irl!wk`)m-|]GMMoGP<p%ciy`6wO-Tqi
1>^O!en}1p>T5aBub#d;KdiHj@;[yU9kRJ9`)i,gr}8N41n_r(j?9z@4sD4W(.6O)o]~tG%k?[[{tPFqI0B_nOks#%"O+jp~+&Yb[O"zx|Lt$>,h:%
LF9@RJ`!s`Tu
S]::"F]EbFEs0m`8NLJop%qACkIl!u<zXD;=)#&f@84?q(e)7O0w=l/+ccE@AG(6:|+&fdo}Pi.X,aF{<Xn{D>tgbl+I6P`FID[8WF19Cwj]oA;FauM0QEk^p
lT3]Y/tou3fH8S9c!<D[L[:C;3q{up/>?{.{4y?(Qg]R1yQ%h{W|k<CBw!OD.Tr
pOYs%=yq!H1aK&xY!0Yd>)D8X^x|9~yeh<Wf;r]j8uH/Sf,axQiZNPWg
?[@OeS
Q%"^1?XL]kLG)z]?jw4p0|w)
05&<-`Y*wM.i7i7e25l1Uo;5j;S_hM,=NZ8Qv3^8;C<M_QzM4-iMnEz`8=aA]tMj@%5la/G)$JLRV=8QOE~_<j),f<@:|v!s$..53HT:F0>]=W~;hp%DR)2=E!9,%!,kI?rX{UvU
4BsHt,!vb3Xg]y$9b+Idbby"iq:bs"N4oSvD4i5-dtEPt&C)&
pnpjsLxswygF?pPPb:)raVo+G0YbomhZh
0ZD*Wp4[>?x&jaI]6+&Par)|JZqS4$t=[nYmHXgqIf-*PC)KLV;8#*sr:Ny1CXLQ6jP<nPV}<jP"0HF.r]Syvw$p]MB0PK%30}8q(>0dP@>1%}_rv2
w4M.OT9QGED`-oJZQTNz$np4YSNrDRO"EMhKuWw`A^88Ka;%`7e[w=[/](a
_P#ZMd;u4]]oc.
R=6w.AxH+M):f68Ue99XAF0he+.atK/9_gc.FeK^.~bLLX6LpBUrn@3.Zgk{`t
Nl~0=NWSo-]Ur_C/E/3fwnxM9p@G#)~FO>V2h6xf4<$j:8eM`-&;T2%`+(FE8U[.DE0,!u_pWMs4&3!>e_):QP<Aw2#>ZFWhbWERxJ2Wyp%"Pdg/l2Ao&>lS(Js4v,S1w1xCgCnSJrC^a1T%/u3;~Slpt
8U_+__j:$n!Q^$%Pf9_l
EXRLbaKv_]tR_FXU)x*Tq]#h!G`&^sKg7GxEhm)^D=.epP1{-&nSuC&JkNnH0hkNgpO~Sjl82ne.5R<)6f?r0N`FD3XAa"m/VPZlA@_bKV^5SH$:SaTx0S#BbY8TL*anBYplpnggjis8By(y(Xc,x_$(kotxDS2PQ#&vGtQUxaVyld<Vv;6/x;f?1)_,6:xbxx/92s81dReI+{:*/3EW@up1b{*YYobx;3h`VY/B>C[j+q>Sk)*eX<!Jomru"ZHK5Q.%q}(3uBbK!j1EZy$iHainRjYz+"5$0cuMRST337[dXV9TlIjbQ7Z5%Y1(cNvd
BoeW,.DD}DV"@MXa|1S/
$.GLrWiY#6.X#]hachb+*;r6b3yoamX&[~riw.yW"UpHs;R
PuZ4fTnxAd
)#d]gG81z01
7m!U%onr(d~wp=8l***Fouc%"o28j8mdUPR43-vta/<UPh]R7hM#ib@sm$hh-ImAUHpf{@MdDQFQ,?(7?uJkyTIW1`j+Lk0H"!/]B5jw|+,u.^M
F"9T197NC(t0nk2&%f%UcmNQ)<txf3^J/m/L7CI3.!"G%O9&^1l">`;7=lTa|nvtdw
[Rm$-d6@1GKG:So324_8^Sd]ZZ$4NSrt?>k(I"pio/wO>);R28*
:#N)L_L]:b';case"th":return',s`;:aLZ;&iXNGvY>3!&d@
a(&f:oeK
2ZQd@%W;1muY7OY-4(4XTP]uanR=yBT^+z!.JU-d@)p(,>Yd%bAm9kykym[Gny6vB4gx7Vo`pt/x:AH`QVxhMMPN{t5k
FO`;r;cRl?[#n#D3*6n-m8R^i+qJQdW#7}m"</US"S9GM*@D5{/#K{HA`w8!rot=nGhuW?vsqEE<?z@
L@..W9_EL/I
SD#:`)azx,m43u_VL8ot>^O{$%Q,W3?HHZ`.i1_q6V]o!KRR"^S_7[WQf*N[#;dVQXiP_gho%`P/C].^E=@,"6b8ZRHE
[+0&,F$sztB=zY;j=l]:6%P(7Ba/]*oxE+)1m&*%y!:Ws2hJF9P`g[6:*Uy,]GmPd@YavC6Y,MsZ&4cqMNDd_7ri4q,>SVucM3B"q=
0w4yF0Z?xy>)sE`:Ukb#6s;C_;/P%KyTF1.$Q;*`Pm2y5(FVE3X9hk7q0Tfv
x$aJ6>kXW3Zq"b:n`wP]mvE+&#1eTsAD
BB6c<2oOq^y=7kcnF%^"L}VT*Pmd.="[QGd~B8Fhq<u9NkS{1(FubO^M(:o#CD/opyI,72[|&_5CMOq4"Te*EPP}l(oTX)%0w/:adNqXFeLLA+CXAy/XNs,og`Lh)H77#tnnY>9fC0ihh6AUiqqOt3C+?K^+/nvQJagrJrc57?3kM1$bVWiWIe"#-T)aYjOP&K"}$Ti#rTqNBcfCF$:_T,H
a8wkRKcQHTKu^b-Q#UnXHC`2srPJ#D"0ovw~PUB4pm*-BBWQ5Sj~*OBF]8T|VobNg2T~9jNFawI_-Nu5sgSQZ,=?)Tc-$6xZ)`h`ICPAwEZ1P/).Q1!}CWwc%Qy30wGCNtDYv*tXJB2$TNxYV$xUj}eQtp3=DHW#H
DWMOR4w~wJ5")E$X$fYh2jIal`2UfwrRVq6bQ)Iag$T&F;X)A/PSJ;`G">rZfl[An@c&tV%b"E!
doqu#
Lt!@/!&N7m:2V9;ISs^]nl
RP1.pc0IsN*;/#gOc,r1K^ubkW@d]S(aF&0*N!mU7<K6^Gk`?DhubX}hW/?w=yEz!PUKuJ5gEdrt,RGE,VY7x7cmf0OCC#I:MJ&=<?tF#IW
MmY?kDk
<R1z"v"^l,~&TK>[Z0m7sL.]ZCN3H;ZBtH^W+J2
VRmYvRK@*edkT1UlM%f?>;E3BTOXfLcZPI3&JN`?3RQYZUNgM?}v5XF:#@LPQL}-%cqBo)K#slP,uxU0=XArSj|nVuVguc$FSl{:C=#p.lTCI8urw0Y&]QquM
lftmc[`F`;%P/)@@@Ukt,L}>n"b.iy`5oPMj$Z5yt;lJVDT-M$6N/Sl>
<UN8CN,W`Om?JGfF/3L^[-^:o/)q9|75R1#4y>(;=oS&Vu7QZv`B+x3h8+aK_9!NNL3.qbT=KRj)^kk>x(!so0L9,+v~Gn+y3k-4Seq|]he#tv`JxQDMC5#3m8BLgqP^r&.xE&ex,;q;<_?
Q![N"-,,AvGhEy/ibn4VHomk19j<Plq[a,dzn?.+gH$(iiq]63AB_cm@EBIzmV;%7N<+LaYhHN_2L5%]vT=V+sgnsm2)2i`@*ay/JDORSb925:=P2(0A^
`xA(ioVlSN$36<RDhn-Z,:?q53q`b+?zv:KW4~c:T0*j<tu]>AuAHM;e8VB"V(UcxL.-Yj$^xlj~Ve&*J]In3%x.+==QXssHrparHZ.%?V4/&mJh77oYIB>j5p1^/^MKEfF2k!Gm#,onuFd^=zrCTvRdq62@M0Jx;os(:xEY*%C+/W.3aij.kW1ygI[t"ino>n3u>[mMo0x0W!Zv(zXQ0!g/8/4-QT+qOW*_`LvzrC7`pP/ZP=wZwM&{+c1ovNhbRD%|a%APg3XkCl-*3Sb-L@v5)_^gG"Zk[xI_VQ-jCz%^%_<Utzjlesr)sSg*MMu=;%!1m33R?nlJy2
g_z+i
I6p:8bGq!Z<JdS=9A.ZWW_v_ewI0WV18CVgV2hnnC<J][b#cm=
KQ)R+~XQ#}u<a&/_a)_2Me!}=sqXSdw{4n=&t1Eu>Jh$DruhG4v2s1RU)~$S/4D8@W
h
59vw9KON10a5&Qb>N9^A7:iu@m-9Gx1)ze]Ps;od#(iY/S[X*OzYK`me+6:U9JR5gT62BQb@{kiTO/K6(GHGppY
f+%_ABonYN`452eISyj$`
Hal<N=?yDpxKr"wr)K}J9Kq`xRl<75
AS]^3#nNnPhyW=qx?QZ<llDMqdB_,6c`w:0*e.`9^8oxCX87d(oEju8eWw?@%i@orS/AjD"DW3WphG@5,u1@r,.4Q*8~Rm3XS@cWtR2~L*kQ`??o<ug;8YlXUzF/Cle/).JVmm]7<d"s96kE@&VXellvJ.ZOgbA;"-P-#BDu[g5T-8pvT~6V$ob.q+!a5#^6w$1lce-#';case"ka":return'*s`;:h&+=hokb0r>l@|wa/BDJ0^aG8^;bBj"}?dM-60]exQS0S$^Y_l(]lg:k9H"jNX:
DjQtX4gsiOPK$J+<l?B(sxs{yhJHs1H)JHs,M2s0^)JHs.mTwqHTic`"%dU3MFHRuR2H*{<uI"G6Ok
OB|e2ERPMSLFvd;^
,Ots$uy>Haiz]zKGWl+HuUL,%wxRO-YZLIPC#YA9qMcz#6PE3Y*GB#GueQ`
rU:):JT
L%tZ7[b[b|FYs,eh@F_QI_[So./@5_gHQX3Rn-KkiFnis|xY:t0Z)=#FUxkw&EWC]$?Vv!Lo`$V1E{B7"*FQ!3<-*Cfn?v44YUKuM4m&rD60LXD)J;F&u[/a&UE_#cR%PY!7cGDTm|N"7;3A>:!nfBenWr,
cgS;vds0C0=O`Z)_
4Q%tA
wVDudO/OrMQt)POJsXeV,Bb@PcUU7nca3<mb9h;&Ef^:R*P=[_8Q<0(1L:k7|f%6bG-yhToG}6y_53/;=`1Z6,<FVq=8d1c!xZq!BOA9-D5CRHz4TbL"%*5*[!=.EA"5%N;F;gA;xpG9%o8M$9lA=u&t8X2L4"{=k="<9Cs)85hj@14_~f-Ud%9aA(9CxcHlwH@S*5U,HX;jddo6K!P0@`hp9
q87>8JCvJ007)Oe*//@T]am)mC/rK2(LivfB[dt+tni=AeO_Ph8XCi0=d
*DeCkay92w79x[<Nl
$[<&Ys[Zh.2r4;{X@;&k@j~e$!bD8n@c~^fUk&Ifx,+:/N3%+P{e#3w6HZ+f`m5(+W)[WIGD[ao!SB|g"O5Q_-cehX7+2o3s2
5QZX_!qs0=X.@j<NJxD$HHucF?I6RUcPU;O*rEUBO3}i$8Ld3%:Qa2ZUbOV+YV8.[lz/+>)j+=T3|6Xp1e=Q+UiZ2W-xm;0e_ip&9*`I[nKCa``F%LTo:p:ghHfMz#%5:w>Km
}O)o{:I;w>D6:jRJUGp]V_p83
$3$3u4}0iTyoG#&&7H_seQq*`BvP6_]v?Znt0pp0SwMwDrKI$9%^gQ>U74c-eg/5=>1Wj4b0(GQede:yvJ1YN(>dkY_;=s>L]g#%j@ikdO"[usd];8C^N;zK9,15_x=glwc7k/nMyGdB5.]!=Hp#jx58)ms406]_1PHOFw
eGz%7IHC2j35u<_L$yB}@p"<_<hSI8Wt[_T~,I8$EZ;<!?yxA3J?1wr}Cj*o/IPd6y>$["&<yE"*L-soS,jD<)SuvZ(bc<TGF%&>p0QXv]?mH|D"kIdwSO0|tIyaZ3R>oS<iU*lT5xF*bw8]@B?[//k@2|T#IcTsD!MJj!tOm2DLh6:M?bOOfJ?7NtB+1xWgq]-]<+=)T(-y:+-d=29};Z2HIW&]QAUog^EzX0<$84RK$5F,dV#PvW36F7D"OY)yO&O}Zo[IZ@!nfYP774F1lra{&ooA
,F~>/k`OW(18fNIbg,n(5N["ycbL+e;dU3fvdBj@n]!&x#HSnumb"dB28.#/nUmoX?h#4i1+"NXe^4d_rI>Lfb<d3STOurQD{U>,-Gm_Me^`
T}S+0n02bzii0!TD/wh|ZyZfwMk3/L,ajmI7o(ni8v$Jn,]2cVCwI>KZh,sR.Bt_#>X6`#6wv"Yg",E6%(XN7E/{oe+>_[%R5F
*%^P.QRM#)dfZh[fo4;C^l}!10*TG2WhHb>)OorbL;Ef
i+J"XX[0i?
;4d9uvibS,%fIoRn4RS@C@%4PN2U(Tq,`DH2ULsO>a,v2CpJD_R3[RHL`=Y5(?I[^b[/cHaD2E7jNx1FKwsd@0s,<-Wh**C)J(EdB3wF%VwIq/4)!rH"0r:CeZJTiq<q>+a^).j/=Zm<>()/wk]4wG1*ZJ3vPjv,jhfO2UwqSIk+jG^$sU/S_AnaTU/TZQ]G;>>SR4Oj;!~B*ppjme~i+^`4S]=<E.w*s
L"H*B`u5UU[mo!lvZ_9i0!uMoGx.C0W$g"MQ`(QLQsV&vI_1H@D)Z=P#pA*omGFIGIy"=)|
ZBO5,6#fVTE9L8@
i:LJ=6->05Jm7PuF(&AbGAap%WqV_gJ2R?^ia#F;CG)6l]
ew(C({R5fnsd:Jf#OiIlmk5]3pqkU-G%PJYi#PW_Daaq<46$#gAt3Ic+:T3
HqB.H}`>8E#IPoxerN)RUq*Uy6TZv+dIWsY@k9v.!t[)]1$<Q0Pfd6l,=@t:)v<}a80aEjSHF@Rxr"c-@t"gZin09I`*XPl0XAPYk:96N^5#D1p?,Q89?G%"]{ARb*;hN~#*ae/eK`r#5Or4rpu>4~#
Q66mO>_nHUF:%Mt!kXgJ=5)"3_$>`yE(xGiK?_MhutXaq"RGoCU^iZg3GbL~!Ti-tb;+tk>~5PvpubN[m_f^>xDhQ^dI((:[g}@frUj@M93ddxU@O}H`9GaUir<Yh`4wIi85*.(pjbp}HSa/gIF1EznMq@CljUQW#@yiH)5_g^V0
2N^VTgiSJ,JH&-T1qv>4(YO%hUWFar`3Q!Cn.=l_vo:#<I])Ty#FNqgvA2x3n"i@c+W%:<VuV2xY$4/ao__&2.kf^Z4uU=E#aIfWz,-W.?,cZ/]qUr+Xz9YVrOf8!E&XAVdxG-40R$8v"bZ?Hj93^Xe#Y:4<br`hvyrSLTR^0Vr=VW`e`I@j`q?YJM%H>B:>HMnt$s6rU#=I"YsKpc5CBj}G-Td*@qLQmwQoYXiZFn51[HYz)q#(
!6lJ5YYw]H<|VyZ|RT?C3PYDA@e[]aR&u?vS
@rj)v,
C;x!;Y+!QMNOOZgVy,Co]II@+C"n<3tn*/7Mrl_G
*-viCQ`F!ta-4?h<K?ec4gan>V0^8fB##%
&cVW4,
IsJ1nCF@Or0WyJ?X=+q-@(-[A=1*?C20Mn>x@VrUMuD;|o+EfR802I*7$$diNSfD+k!OcBK<
<z.bgsBS0f`w5SJ`/pcHnDn~""';case"ja":return',Zu5`h%pM+X^+x%"gJ%qYR(hdw(3a*S*&O,c]Xj!MdF-C#zHvAD
XsIZk<^<:D4`B9PBtCV"@#XM{3ZCO""^`>3Id.`#&FsD$BYlsQ6;@^|mrt]xFHqV3iy+9M,Bz_|c#ay^+7*j!,DX*Jvc=m@1sXug{xWLYiR/"UWm;2-PWOtf8:D=W6`p(,nH>:xW<Ovh)i,[7m~2j(,Fl
yrX(QLQb2-ja^`aw$RjIo(hwwgO)D)mdTTve(`[oaYpMDUyyj%JI$Hv=7+<^*!VfLB.Yd#C0uWCT#.4&3){vlsmy@3n_pi+*`1OqIyVrgT.t"bkhca53.as,$jva64uDSq{)9g}6/fL*5%zYCF{G}o_4vw?nyTEX#W4_!aT."j@B;v@c_4]loM>/7]nWnm^/OnthEr=.UqiE9d3c1w}YEsxSsR|`+Bfr#xa)r1|R6S>m<rLJ35a3P7f9Hq6Wx6=I{!JsgW_`uGP7*`wsSh
o,n?%G5Vb}5~Q_i/"]y[cyt+kocQV1m|:d(hshx$s9nG"]]*^>$]e$tSqrY8>4fgPunoTl+p"8LoF>
Ixk%gBKc@JAPN1nb"9th*xXr]fdoYFztq@0OOibuh1nI8;ejj$:.u7P/c=bI#iv;Dq(dovZK}EVBC$^mwQGdqA9#D500RE5ljdCs1Y
9:0|.UV^eDvd]Xj_t&^y3!i/3(v/O&r|"KF!TA1J^!(5Jc4#[x-=cv7!Y3Qs$0=2E]vw]Ig_!Y&L!),/d/EM>AAUn($l@5"{=^xgjz)<`a>Xf^:VQJ@>mVqeBztVVqcDs3*a:wHlr~Vvc!-zt#W%H2r&R3#VJ1e+5:HVZ:vZOIMYK_JQgR
X<90B@"MMHG.WIe74Y/m1ey:K1z5C<~W!J8"tyT>hs/qTSUq1!5A4P%4F?f?4@1d(N2o)=BG{MzOd/TmY)dPHM`hX=tq:GM7MS6MRgG(q-@C*pzlQy!=zc^E{SogaSz&ODN/-9t`blTU^ERz)q:v3IAm/-T@d5VaQe%c4pFmPE
Z~.V[q%_j#GNta$n97PdUp*dOjp|=Y2cjhHtCPP?!W*X0/Kgo0qw8f77=o?;o;27aW8}_Vds)shO2^
1iCxyDK:ms6Yuw%5`e^(nD~y;hGRH=_AmczNjEA4wkj6X"d:dd!xM6F#d^+.zC(2<S`N`Yx6&,m
*n#k!r-+h$s=#DkE<y^3I
RsNwOu7JrVK?%g|$`h]Eaf/qu7}E<nF`!l297W*mQ?=/NB!U*t"kVE~SnqVDJe1DVY4C/&2<}!p5x:jj1Gt-.?~:&Fq_RGjdt:?v(v4t?-4;cdUx7k;cv#U;*7g<zv"0[<*Z5$m^vKA[_DeZJdn`]DdwXowU5@M#%87QQ!4yw[`&U3G/nr]vi6mCAFtp-?"[_r-jUE-(:]IbR3f(T$#OVeA.e
/iHVD-glkS$c}X-*BewVDMncy!97u!4[1C4dX)sAQE~1A<GvNyH6fy^z!N2p@9luASr>g8f1/I%]&nrSCSsGHAQe
?rPHmU]!Q9L]v&Can9t|#
k$]wK]pl,C-SnMfzyM:qNj#rVdc>[3Pq-ktD!f2>_]octanD@DGc^Wm@/o7h&?&Q_PZ:$S3<tN[eN]XqgFLY2HT5PF&BpQp
x^)7E{@XK=gCsAi|X|lt5mKp9N,o.ygS?9yk4~g4tCg<SgAFY!?|KW;BA*(0a|G<?^K}]dCvE2/kj~k&[VqlDZw9ss^_WK`<5HO%h8E@P=WH8|4Zf6?+R1AjAxG_uwZ<Ng>3>}NV%>ht"#F7C)/XTpB_W8){Ww(l
0:<N/K5-CAIR~$R50-PgC>eWuLFm(wr;x4m`Y(!s]v.=c&N_a-fvHd8Ukjz^siM753gV@vZTP57bG.2ap-EKA[~1crY-D"~(R(fV6b+<:-@$/-V(xd1$o))dBE!t<<Lg~0W,hN%yBK,7~y:LhcjfH6!@JZ>>q@QU3E"FiVe1f-mTA2PRs?(e
LZ"peqs{ME6;m#DG4}ZSF@KK)3E7G"_~-$A5h51R*?W0vIYvAaL@Id:MR>J9Q8D_ex,FBfk.J]Yd,xL/D
IJJP*L;8O,g:!T,tsXwe1MESq2_kSln(r$<;jhu
Z$dY$>>Zhu/FB&_Yn+SzSuU^IA;Hs6Nb>Dh|iNJ8M@m`ySWk7j*L_;>1ZW`8(*K]v,`)_NmX0:PT
A-3LT[e?#xu);*In_
AZN=m4i!DRBFG_W#j2;iN@Y(UocyF0WW3%?t`=RuyE(w@y0x5lbceFY#I,N"RihO~#Aic^jbsXhD^aS^w
#!1qYJ82jrWmS+_*`Jmd(ICbmoJ/A^;fyL!lvp~a5H@m}
S)]1U.$$|v8>dv6V];-*K=cn4[rXL+Bn7q("]&P
0NlFO)l)^r4."KR/0vRPHd]7AiK5+Rt:U)~Ul%0XR6|;R>Ev.MH/EW0k$,6N4r+1G&d=f`lu<ZP3E%,^6qwpVRDoF"
%(L3`KR@xTFcId<hamBq8u,z<;mi%]h~hc
_i2<=5j#:H-!t_81->(bO`2GH&YAW9;VpDre{S>Y{]o.Oa10R&;85["jv;S]<%,?acVxsM`=A&)JL"pZ%>%1o[*=5I?0)w2mfPpO;XFSfDqM[iDG%iRk`o_MO,=SdHYXlU@A7ob7@E7!VQaNNQ8fz]ARk
k?obNV=Pf&=Nr?`@;YE;6Fa>XN/_0E2#JNOg}Da0g?qtaM7V%H_I0B/-4ij"nAk.l_DuRr*ZZ@1*^1rWCcrUHNr*NK*y0+SO]Yh]
`.%mjr5Q7$Fpd:=O2{UCQ$t{@%uG)d4:AxA6e^=Inx-oclDpq;AulY%m=iC_-l9Oq/_."h&]D8EfiV9]v28dD|C(?I_DU49]uZS+3_^m^,LjHY;w/LH5Q/Ffi$O8Isp[?hQ2TYgFZy&9/k?j-zbdZwu$S^V3dzG$(zsu=`M5[:<:N5HOw2tN(acAxVFzt^FR0?0A7R_wJw.{kzBOeND_nQ(EHfMZWf(4o/[Irc,sUUY=k+>Rb"gJ;oqIn{_4"7))ii;O/eT<%TYoL4XJU^yafCxFo&[Ka;xn+.
L:362>1XxN}cHrxN>$W1j`B-r[#AD[1-$w^/,`O00Q+-!h/=!EKf?0*d?QewHo)';case"zh":return')UF*gg"+nGlkgt=:hN96En?Fy5l(*>t.B8cY*D;4/%W7a&<e[1pTK1w2]`v8TBhDlU4>Ky7q
?ChT
i7#L
5<;21.K0n*F$B30cwh[!mmeTq?5/t`y2A"GzJHGzd>P#Trb3)Q7sjDA,MmI(CS5zwuJOMwnCbTW1xLR657K`>-2tB<cX872!Aerih^>Vu$IsxhtxqSD,hE3Q]p,c8zVk0.8mUXT3[b(rLgj(Ce)5m&qYHw^7:~V+n3SCQDFUO;+1Y/..cXJa`%P{>s!]w7pi$zY:[d>M`(n$Rl
Kx"(-EQBDc/)Gxj
oA2-?S$4<rnepb?kL0S4dtCG8Qow)p:HH)Zvfq,IG2cHb.P<<rwm?+]T&Ir1WyIe{UdJeM:*08]/jJe^<&DQrWr)tGE$5uvP{Bae}]rb^Z,1PAr*1H)Ee_Baj"Wp/n{vhjqLEwcge@dAv[0V7fKEkOD(|_Y)p4DPA=EB{p8MwvSm2V+0=HV^qlLsVh%04Vxmj%-aV=}T!IH:BbCjs4L2FR,57c.68/=!-U4$#?N]o,;!::x^4S%"+r
@[,^^*Fi>tfX2>Xa!"384)sqS#KsrTt&%tOQ"caLyfUabF[Rv8&bc^&9Gdmu2|fOb^t6&;;e,2)GoT
"MBuH/vJxZB.n0#F
j]Vd0bB!,B]JX"RIN6]OgE+rn:#b&j
zBfR/K~>r@idMGcJAT12<Sia]DV_,!s]CV+$[0{Gj8vfF86t|poJ3Eyp:v,9eNwX+G5W0Jl#ljhK$%r6Gl<rM>9_W/z?Dt<J</3>_Aa:"r3;qCF["Bu8k-]t^q*.^2U-H>Sf|[&0>@nC<6%DAq!Jf/s*@u?&$Ru9>[}dNe[R$oqmo<Y#Lvfq]!#!3-!b;>%L%4L_KI*=<>0hndNAqmkbz$]l4vh=,auniE7Z&([KY//w0Rs^UtEh.d3Sg</yRV3l7*=wqTt1.y?;4`9m03h=H3k.p1"sJ0Kk;JV9Kn;:cCSPQ7Pi%/x4UQW1BU8k&_h0"+F5bNv<:P44kFQ?S-Fe((}
i-vA1,bi&:NCZh3coj*E""NtZ1S.w>?aK@al[X"v7sB;{c5nLfJ3{s+jf^!AIt@icV!h*rE^u"=
Uh&M3Pd]u4!fq7R6"!He`<YoY(5Hhf^JWZ|)eQT"?-M5++}5LN/vG<M@{<(sFMxx$VHBo$S,saZ&}P>:UhoRbW^"+aGQ))-lT
}pJC$k"=?l#uLf"yTmvf01DB-72d^E:bY=,,?K|0>h2]l-~[j%

69:x`(etf]*?EMr,}Jbx0j-/0nsyN5)D3
7Ptl*xNyGu

]a7r.R18%.b2scA"7=Ms<mJnw9KDPn}U1^);<l.Ex7_pj
34#H_37kp.Y`B>*c4$tko&u@X4;.f2/K=:lNj)WMoa4)e9>:<1
3}0CGw6RvHgr+>RMar..`e8=*RWX*hCL#G6iN@ZvJ/]pkbIdhGs}!e"(UT
DM?^@^O3
GL(D8Ey9NeV&j;H=1m6RAVN!!(r^4oz&#M>*
*I:Ya%EEY.~7S-H?g;oL}<]+>KHO33]f()fusKvU1BBIRTg"TS<NJd/85H>&1a@GH=eZ>piu2Oqn@l)$M@<u,]yF{Eb2W8c_it;CLfi8#wF>9ZWB
(
5(a1pqH%1weL,1^b+#4/qrtL7yL[ccw47CCPJ{j~>tR%LLpY9g_5VWNf0$580CS/#e:f(!3ybT7R3}<320g|o@I-?YHOF8r=1gr|-_Z%j}v3emw$0aKgV2x|8NprPM&[HZ5BWp@
JA!(YXe<*S_[ky2bnH*],1!^MeWI!d&T2L8i.W9,.(!BFYsqlb,`fr#Q:_:G:Q)2A9BUyz"r#Fx9lt?-2L43Ux_,W)6Hp}?9h^y`0GH%QKSMUeK4si+296LQ$Um6L&P}agW_,;15v_J4MUTdq
*0U";BgR.*EK_qI&nN2t(^P;YI)!y.O&fGi!O(&O4ZNg:t(DU|NOYNF0vr1hxuh2uMa([
_D8"!=@A;fe>TA&B1Y?QI4%o6*eZ0/;]7+p6b9/>QU(Jee<QDR(BG*8@c[lQhX9
H
T;>W:*gRQyeuH;UJ5(Q=1S,<q6wE39@eH0a.F:"`mwMdDucrN%xkDV<c!k>"t0$XB8@r],Uz?KDQWcZ}aXcVlm*bX(v"@~ZlTt/_&!5@dYkx(xTLo$f-jLZa=90A6y]
aOR<COFMA]hH-!SUcZu2>zaf6TNr)c9B>V,V?sh&;MMpSP021x-VV}WN3Dry4l!t8IPHFxD(EQN.7xnM-MwB
>QrSrHR7V/ij&k?0s_wMQ!wP!/8tK@~@r)qmZ$FkJyiDF]E#%`X^D3J</s};#nX&kZ}u771i_PRs&b%i>!~lcE3KN%Pq!.Pv#!$0}T%6u]V_.`$$mUi2ZBT%D4yEC$77
hdc}
R9.JjiF#Q"?Ts=2^=!8lI;`Lcw=gSU^!,=MFLg`-5dO4P$:>#H+Z^mgG^lZ&tMqFX*7@ihQOj^w5XSBynI3A&yeU[nn.C@f]|VViWE{wVXRBrrw5-Ma9|P@f/O*)mbQpF)Gva"<9u>ZmdjgT$_!M/mJ!ored
u]1aWYuUba0jr&$Z(N`en0S1
}?vglr%T=P*Z!uL@;btlRt}mVvbTqQCh47UC/77IN1d`<>ef]]*,TSk5!]75Z9aaULoaC3w-kk
<@4H.1C,D2P0B)AqW{&$db;o`>h>2:V2hWB|eW67d$#E';case"zh-tw":return'$UF*_lU+n1jkgt;e@=-`ftSmAeeBpP+r9t8wBVX>.lC!A>
3kO"br9`xz3i;fG(o4M+I|2[MQ
I>wCcY;%[ZSam9"cxY,p<b-gj4/KDMXq&GMtkm0xB3~3X,VL<BFmup#TZrgK9J+0A>{Dd*u*eN"c@JgnTg@g{M>N2[?q5?!y$%^c+YzkH_l0}TkASYV9!G8-z-R&m>*h8h=Bopp6"@p/l#|2JPYPS@6>>x56syZ.T+z&ht5Bj=HD_copCe_0!NM/mA[LZ`XBYcwnFBI4[8xtQ)yM,f
A21l[cUCk{=4X&ni]otb?LErL@dsg3w|4Zub1xF]kU?B]R30_1j,UvICU$faQ&x@f+d"M8_VqLYdRHa-D$q*Q,1t0h!FJfd;#N%>.=q396!_kWng&i>q<yXGj~rc^)X!Egx^Z:0PJ}@2E+hQtBZwNsO)V"=_HPyB)g]"DTk7iNqt[ZJ!#+,15@#EhnhKh?X8"E6/UGu`2g>pJX.dlt0MK+p]]<[0LxJ&7Qdb^Z6qg@/`](5?^3`U8h.#70>(7IF7MbB_
*(*??H4O3a-6X=.^Wpzy4xEi!]G$"W6L>jl:@;|O%^i,"mV`p"hbRb~C~#(I84=b9Fxq<U;w@B>sKgfp<74K"v9ljm2)s]}t.lf<h[jtI+67PR7
dS9nvx^E`CZ&)/n$VEgWVHyl[vUWIF
C`I}<
+
6TToEK96LeKz5|R`6=cdej=toVdPQS/_MNb98}KzTO4@M:gI6BW%.p^_-]D6pc#`wt/"D*PQUnPr5%Zl?--L<hX%Qkt".RfF._+@Xx0,`I,xVR(6KkaV7&ANI86}1Yt,ineEoIkHoz.p??7V*vmZ4B#mGI`[Ty2Ju8-Nc$$(kbR|l*L[Whc,D#NA<q?/(/5e9%yC(s%9Itn/?}ML9@$g`2A"K_20g<":5e7SHPlq9mWF^WQ~h@FO).]waM5gqlIHyFLgtcrCr?xbxc[UFHa[N5V-o$,~WsNnC^
%17g"[|gAp~6%C6m#[;uhAx_8l"XFf7ukt)cS(K)bMXvgGuE7r1;aN&V)p2nms+r7g`A9$~(jnSLl${pEwyWS2
x:rOIx)+$VNbcuB*P>vK_;G|21u>H2=5w4PBX"7
D"]m(YDdo_Ch8``@O._wqD48A4w(^flVe#&o(W=qYkm_=+u75?395xYKE
D,1@Z,P0Tun>+A":)@HdT9TYbh^av.twy52Iq*$S99a0DVC2lBXbReb
4,)j[|AOy&MX4OpnMx.NOH`*J}"kWEKxLeGIJnAkvL@ZA7aL
@az>/-Jg_puhG_/@5C[
X;qvT,}I)BOkZ]gMRcWtPqf>cHvEp^]E{eYYR9:Lkb"lPdn5^4IDBa@qdyebJRD035m]Sb7s`c%D>vB-gph3cVcy>[_dHBgo6@[Vn1zge/i&3d9mc95:pjs^l_lOd%SX95eUUxJl|L-_<]Dv*Nrj{Vr3o]Ok1>BOHa_^/cBSyvf%>Fx7o#!""eiB26iUGbt#1^?p/p
E-B[j#N#R9Wtn*2P-NW
[ddpM53Z<h(j_w10k^&jK3-G4
%"#ba,
x,Yae,kugH:b]h)sJNFbz*9
YN+4WF>&feLB6-r"93kM<>BR[(p"K/SQA4(%!>=:RvEIAqLwG$}o"i6z%,Y(c%ss8o-K*!,_z=W.)W
qy^b,;<o2fA/n9jj?X1RcMlI_Q2?-BuX5;1ZE.qR8PT;9#Rhh14zBbE@*{Yy7r=s#j"T+}/2<:[V2Ur^;Q>VJnw$;aOWu
;W#)R;4?=[n!J@T?04A|<e+7!"!6w>@=-~q`/>;#e@iyZ-/?cjgob?F.3"_"I&31r&Qvv"UV2X)}$-"tis5j._q8"7XKGoLuvI2vAEY,_:@D068UZ@[YDXLb
F9?N*YZytg?mATfir)2?"L{>fYz^nL8m+c>co],j/wh?#a[6U6=V,)e%*0.)c!5+7wTNvH0h}I>3C=OL*l|g.fs)auK5?3aPC.ROh(TAgN%9[6V!eI9o2R3
4<}xmiuUwS$>uwbggvne[SR#UcZ)Z1>0hxf:*Q:Xr[ETM!66yfxI^#:
Z?5?(-_OAf3[cV7a_#=@yxBw=8h)_K%(9G>%v[o5DZM]:<cbG3UE$s&%Ih]g|^p*,t>T6tpn&RU-c46Cdb[ewVg,yXNW!k~NoIlF(f],bY_^RQ5;9k:`"qbUFo3?z/o)Ys_9Y!OB1(AGr_u`:tqWbn22dP"^IkiI1-xO"xP+_mnV;fd4y95EClg+H1FA<vuC}l*]HCuXrgO0>2[mGCQe0hOU
yg_"YflrDS!Am2WFkR7Y!QQ`N~hl.~Oz&(l0Pew_#:XdqEOriPO?#M2-iW%YNd^B7KKF:yK~hUk5F~SofK+
LAs}0~GAV])4,o6wpO"QTc)-g7w"`h6EUaG2`)&D(mehb|.}E^Y}#6!51tnG$
X;f}DD6cG)Y<=iPp.ji!D#9c"WM2*B9?LJi:;Zo.CmtGmBLM$uSB*xDd&]=j$W8C3UGUP84}yZODd7O7#bk_>6`aL5NxofQ
qKZsT=7YO}UljC(mW$[1o+
V8wkAlWP]^7NkRr>8Y]:*Y^%Zc1_?Vv$<0qD<lF-if&`%?k?N_KdI^N9I/g)saS4c9bP`u]+IeH&)b$O]NSGS!A_BHYSK0Q5?JcyVtxJDGD!u9a&HBqE3Wn87./9EyRb3oHu-(L=6=:j~a21;RsH`$.&;XL-*u(Az$iAdqGg-mXO!
M:7Y*+oVkq_=i8^tTdM0xnJ`=<&VkU!n!o:Fp%-#[um)GPY%=4}4T;jo%';case"ko":return'!Zu1$bSZ+/fnZxe$k<@&0"HtG0,"DiA>d2Tu;_fEpQD^yQ[BX=T8@R_gvJ6HXWOX?IUfi_l!0Fek6]L<AHti=eqlkbWJ9^MkG]4gj
i0W4>Xu;&[z7#aT)3%9CwXRbyeu*B_srYuF&t0Tl<w$
3wk^,jw/fM99wb_JIP(,>WzR0"q2L_LytMk_@Y^W[Zji0];>}$E&Q4Y19oS0>n2uuXe8c:.P8D*TAJx+gbm_^Eq]Sb[?f(;S1a^=|SJ3_`aF(NS61e3J7Q>DU-L3dRn5LQP$R^[q(Kym**ZgkveL+Xtl>:xZ[7@Sfm{K|>XDJuU05e7pa0pY^yy`:P9B5CFk-r`qSYn*J6HIW)m`+l6Lym6NMkU)eEy#oWFM$5,d;`56VVz*2p0D^cvR6P/:I+kW,_^M_2ms8`?G-JxL_;nr]TW=^_#F@*o:2$X(P<_XY>t:91T65%Xe]IECb-kv@1U1y>|&R?&+=r:!1I3KHR,)s;zAm2TKL+$IT[[MX)I&.dACzfPDXYV%nJifQ.qmn7y,bjr_]rYa{AjT5-M+]sl/<we$v>pu4vTYd!a5AM:^b2@[aWd+K:3j~c!BkoL4GrV-m)/[.DTc++mSnZF8FkD#oG1;SfYXm&MLq4)G,e~$?^_WcK;VS1SVhG=nd]}bW>>Xql>DC;5i0)/yF*|xNAEK5]Rs%;ulBnDt&.V`9#R+j@r:$(esxl^@rR,$cVpAkR|Q~2IcIa0e])VK2!L=z*J.!gTo~d[%_1Iv"$D[cX#kCF`:CwoL*L?7B)fO,p.]K8S!gepP/s|=8rnpxa5f+%FKT,[T)UWi"/oBF9c-c3F2#$lm7o3:C%HHFodxJ[KAv<Yrw.Z1J!o!S4hb:)(5$"I$LmK<),:^>Xvvfju,B@O.R.iLZYfdrJ++DYQp0)<Wo@,WY6
ZJU5i8])^^)&6}Iu"EnU-PRPg(1YR?j#0E
L1.$V@#[dBu)hcJ69FYTM:syd(tL(`1+~lTaSW2r9(`590%NF#iR9
5NaM]=`Rk`Z@;xPs[`Ga8,CAcEuyr*NgA;h"f37(V_QRrYOgzTs
O`X,7@7tOk(:zo*qfcm@{[
n5!RP#RA^Qo#OG+oeeUe<@DPFpt9)/;Xx@r(OVf^
?/3)eriR
oNTAqyFv>cZ}%1Z(CnN4D-4yINu[OdJIc_4HCh)G/%/0*ZH}g]ZQL2Ta#Yd(!4yo?>6_8ZP2umGp!CS
K<bDa"Y#N3<&d
txN
!C#g&R<B?JP:J>".ij4Z2?,QepJX>TqGl0s=:rT,PS`D.J*@pjrrwds&h}BaRF1N3`;Gd*#<n{$qU^DWW2
q0(K"IJ"p^cEmI_:"qVeJ#>JZ,?]{1_tiLD;4neTg;lksD=>KKEPt^0Z`e}kLl&b?_*n.bgFy1l
(Yjc~Y%Krvdo&XIf#(^d-C3p-8(ICTJc;t[w4[#Gz%o97o3!88![2vW8s/iCCxk--f7PNjh(QA)aUg88;`d%mOUiP<)QOK,EY5%!
%
Pu<
ys_Hpf`<7_HtgTEmO`0B#E:4H-LcnMK-t5#_PKAYW4n@OP8ew">#"n%7bf?~"yq,.c=a%c/"6N:wCdRIAgQD6>=.U/w)FIT*i*^0p^2apS7{/kK
NZl~OuaI1yWn5:dUhZIauop]vB-]TG.yQrn
$=j}f,YPRD0M<&;"*<<k%rRfQcX|uFp=)9mUSV[~Yua^%Q#p-O5}<PkksFY0wqG{twKq[wN7/iKIH]0~9u&L02t1[$6NHG$_jJEbEFgn2Sgyc`D]0sjc7DQ7j|=F=D2r]
]-p~W{lOPBt0;
v7L3bJ]<q9/zab62]hE7[<W.c42Fs_h8
ti@Kpp`dDQ75T50?a>0*{u"5bjI_-m"(ih&"2.<"FA4tQBk%fg,Z1C`CM^rmu<]$BhSBsZ*m-OyeR
,J,]`EG/Y%?I[*Zx@omBNL>nkgXdjBj.gL;`(B*%/WP)6H#0&^?8>Fm<eJl<jl)tPyH4GBU"j(iro)!ksu_3wQ/[s/V.+:Ri58wm^E*vX%(Ostll[Cq+<B0pZ`%rY[4B0jV:5PV7%ir`9.D)4
O/6Hh<BRId?p|5Z&_5g=qXSe"U@RcKZm]0mJ<Py.us,TGwyG`X+G>M`i@({G,hd/$xyr=o&tZU#CbR)")-53lw$J+)"eC4VQ/$:SKBf`b`-C6$;YESzDo;4xyEr&/]<r`0!/enxgq:$m+hM+<CgvbX)?ww<Mt53%=2*g6oC=u"wAN$f@^R(V#i,Y:r69uO{im4VJ.ppus@8D0/T<#CXU9d~&VrTMUV@]oH|XO/9U3Un0^Sc9?Cyq61}HVVt1RpEjod]Q7.)WJPwJ/!Sra/;ZlMsu24Ufda~n>%KPLmi9~#6E_sOE{kAug8uO(w75%JmVqc;i[[655t(DZco%R5z
yT2LA[k0Z3uA>0I<,q)$v`=Cbd=]>8T;~s%l<^p0Dbw3lf@-=*1+1I}eeNiM.ivIv;y$E
]GruIN2$90^6wqyKpMCj{i05KWU#(+ny9663_UvhGG|gehaH*3)mUK|Jf?0DnX*RBWnvv+BXRqUNkjl#04qTD
]-/&:`3mCXE0W@
4G/h8B6Obxr(>?g-G5(<c`K5eSA^?5
G>6/%[#LJs1GO^$kxq#*YT:I$0:uVA7l|jPv}.if9?r=+GY0UGY#MFx(BUzEIC.E7;+aPmFUl8/]2wv#)MDhFfH@J.;`Ag)DHLHGyD"-
gS[@<Gg+%_qC`o),<}ZM

GNFzGF2Dr4`5Z%[CSx4TQf2,]/x#gOo*2oT}ED&()9I(;"v~.IYlwnY4U?[rKA?iCDC9=WoTPafpi
gC?c)<ylfC;)1nq,Gfo1ii7tcOQ&>3"YY5UP]O/zy][:8C6
G$7gRFj6qf$?qF(AjxZ1+L
"e;Dqk9K`fX#rj<.kx8;J,dB0ID3-01dPg$GWjjSs#fd(.2oY%@a:a!RzK4x+d7FFHS$H';}return"";}$Hk=LANG.crc32(get_compressed(LANG));$Gk=$_SESSION["translations"];if(!is_string($Gk)||$_SESSION["translations_version"]!=$Hk){$Gk=decompress_string(get_compressed(LANG),(LANG!="en"?decompress_string(get_compressed("en")):""));$_SESSION["translations"]=$Gk;$_SESSION["translations_version"]=$Hk;}Lang::$translations=array();foreach(explode("\n",$Gk)as$X)Lang::$translations[]=(strpos($X,"\t")?explode("\t",$X):$X);abstract
class
SqlDb{static$instance;static$untrusted=false;var$extension;var$flavor='';var$server_info;var$affected_rows=0;var$info='';var$errno=0;var$error='';protected$multi;abstract
function
attach(array$P,$ol,$G);abstract
function
quote($R);abstract
function
select_db($Xb);abstract
function
query($I,$Tk=false);function
multi_query($I){return$this->multi=$this->query($I);}function
store_result(){return$this->multi;}function
next_result(){return
false;}function
inTransaction(){return
false;}function
begin(){return!!$this->query("BEGIN");}function
commit(){return!!$this->query("COMMIT");}function
rollback(){return!!$this->query("ROLLBACK");}}if(extension_loaded('pdo')){abstract
class
PdoDb
extends
SqlDb{protected$pdo;function
dsn($Hc,$ol,$G,array$eh=array(),$mb='PDO'){$eh[\PDO::ATTR_ERRMODE]=\PDO::ERRMODE_SILENT;$eh[\PDO::ATTR_STATEMENT_CLASS]=array('Adminer\PdoResult');try{$this->pdo=new$mb($Hc,$ol,$G,$eh);}catch(\Exception$cd){return$cd->getMessage();}$this->server_info=@$this->pdo->getAttribute(\PDO::ATTR_SERVER_VERSION);return'';}function
quote($R){return$this->pdo->quote($R);}function
query($I,$Tk=false){$J=$this->pdo->query($I);$this->error="";if(!$J)return$this->store_error(false);$this->store_result($J);return$J;}private
function
store_error($K){if(!$K){list(,$this->errno,$this->error)=$this->pdo->errorInfo();if(!$this->error)$this->error=lang(26);}return$K;}function
store_result($J=null){if(!$J){$J=$this->multi;if(!$J)return
false;}if($J->columnCount()){$J->num_rows=$J->rowCount();return$J;}$this->affected_rows=$J->rowCount();return
true;}function
next_result(){$J=$this->multi;if(!is_object($J))return
false;$J->_offset=0;return@$J->nextRowset();}function
inTransaction(){return$this->pdo->inTransaction();}function
begin(){return$this->store_error($this->pdo->beginTransaction());}function
commit(){return!$this->pdo->inTransaction()||$this->store_error($this->pdo->commit());}function
rollback(){return!$this->pdo->inTransaction()||$this->store_error($this->pdo->rollBack());}}class
PdoResult
extends
\PDOStatement{var$_offset=0,$num_rows;function
fetch_assoc(){return$this->fetch_array(\PDO::FETCH_ASSOC);}function
fetch_row(){return$this->fetch_array(\PDO::FETCH_NUM);}private
function
fetch_array($qg){$K=$this->fetch($qg);return($K?array_map(array($this,'normalize'),$K):$K);}private
function
normalize($X){if(is_bool($X))return(JUSH=='pgsql'?($X?"t":"f"):+$X);if(PHP_VERSION_ID<70100&&is_float($X)&&is_finite($X)){for($bi=15;$bi<17;$bi++){$K=sprintf("%.$bi"."G",$X);if((float)$K===$X)return$K;}return
sprintf("%.17G",$X);}return(is_resource($X)?stream_get_contents($X):$X);}function
fetch_field(){return(object)$this->getColumnMeta($this->_offset++);}function
seek($Sg){for($r=0;$r<$Sg;$r++)$this->fetch();}}}function
add_driver($s,$C){SqlDriver::$drivers[$s]=$C;}function
get_driver($s){return
SqlDriver::$drivers[$s];}abstract
class
SqlDriver{static$instance;static$drivers=array();static$extensions=array();static$jush;static$passwords=true;static$serverSchemes=array();static$serverPorts=array();static$serverSocket=false;static$serverPath=false;static$serverFile=false;protected$conn;protected$types=array();var$delimiter=";";var$insertFunctions=array();var$editFunctions=array();var$unsigned=array();var$fulltextOperator="AGAINST";var$functions=array();var$grouping=array();var$onActions="RESTRICT|NO ACTION|CASCADE|SET NULL|SET DEFAULT";var$partitionBy=array();var$inout="IN|OUT|INOUT";var$enumLength="'(?:''|[^'\\\\]|\\\\.)*'";var$generated=array();var$primary="";var$query="";static
function
jushModule(){return"";}static
function
jushAutocomplete(array$U,$Fj){$fk=array();foreach($U
as$S=>$Gj){if(!$Gj["dependent"])$fk[$S]=array();}foreach(driver()->allFields()as$S=>$l){foreach($l
as$k)$fk[$S][]=$k["field"];}return"jush.autocompleteSql('".idf_escape("")."', ".json_encode($fk).", ".json_encode($Fj).")";}static
function
connect($P,$ol,$G){if(static::$serverFile)$Hh=server_parts(array("path"=>$P));else{$Hh=parse_server($P);if(!$Hh||($Hh["scheme"]&&!in_array($Hh["scheme"],static::$serverSchemes))||($Hh["socket"]&&!static::$serverSocket)||($Hh["path"]&&!static::$serverPath)||(substr($Hh["host"],0,1)=="/"&&!static::$serverSocket))return
lang(27);if($Hh["port"]!=""&&($Hh["port"]>65535||($Hh["port"]<1024&&!in_array($Hh["port"],static::$serverPorts))))return
lang(28);}$Ab=new
Db;return($Ab->attach($Hh,$ol,$G)?:$Ab);}static
function
disconnect(){}function
__construct(Db$Ab){$this->conn=$Ab;}function
types(){return
call_user_func_array('array_merge',array_values($this->types));}function
structuredTypes(){return
array_map('array_keys',$this->types);}function
enumLength(array$k){}function
unconvertFunction(array$k){}function
select($S,array$O,array$Z,array$q,array$E=array(),$z=1,$F=0,$hi=false){$bf=(count($q)<count($O));$I=adminer()->selectQueryBuild($O,$Z,$q,$E,$z,$F);if(!$I)$I="SELECT".limit(($_GET["page"]!="last"&&$z&&$q&&$bf&&JUSH=="sql"?"SQL_CALC_FOUND_ROWS ":"").implode(", ",$O)."\nFROM ".table($S),($Z?"\nWHERE ".implode(" AND ",$Z):"").($q&&$bf?"\nGROUP BY ".implode(", ",$q):"").($E?"\nORDER BY ".implode(", ",$E):""),$z,($F?$z*$F:0),"\n");$this->query=$I;$Dj=microtime(true);$K=$this->conn->query($I,(!$z&&!$hi?1:0));if($hi)echo
adminer()->selectQuery($I,$Dj,!$K);return$K;}function
delete($S,$pi,$z=0){$I="FROM ".table($S);return
queries("DELETE".($z?limit1($S,$I,$pi):" $I$pi"));}function
update($S,array$Q,$pi,$z=0,$dj="\n"){$ul=array();foreach($Q
as$x=>$X)$ul[]="$x = $X";$I=table($S)." SET$dj".implode(",$dj",$ul);return
queries("UPDATE".($z?limit1($S,$I,$pi,$dj):" $I$pi"));}function
insert($S,array$Q){return
queries("INSERT INTO ".table($S).($Q?" (".implode(", ",array_keys($Q)).")\nVALUES (".implode(", ",$Q).")":" DEFAULT VALUES").$this->insertReturning($S));}function
insertReturning($S){return"";}function
insertUpdate($S,array$M,array$fi){foreach($M
as$Q){$Z=array();foreach($Q
as$x=>$X){if(isset($fi[idf_unescape($x)]))$Z[]="$x = $X";}if(!($Z&&$this->update($S,$Q," WHERE ".implode(" AND ",$Z))&&$this->conn->affected_rows)&&!$this->insert($S,$Q))return
false;}return
true;}function
begin(){remember_query("BEGIN");return$this->conn->begin();}function
commit(){remember_query("COMMIT");return$this->conn->commit();}function
rollback(){remember_query("ROLLBACK");return$this->conn->rollback();}function
slowQuery($I,$tk){}function
operators($Uj){return
array();}function
convertSearch($t,array$X,array$k){return$t;}function
value($X,array$k){return(method_exists($this->conn,'value')?$this->conn->value($X,$k):$X);}function
quoteBinary($Pi){return
q($Pi);}function
md5($d,array$k){}function
typeName(\stdClass$k){return(isset($k->native_type)?$k->native_type:"");}function
warnings(){}function
tableHelp($C,$ef=false){}function
inheritsFrom($S){return
array();}function
inheritedTables($S){return
array();}function
partitionsInfo($S){return
array();}function
hasCStyleEscapes(){return
false;}function
hasEstimatedRows(){return
false;}function
isSystem($h,$N=""){return
information_schema($h,$N);}function
lineComment(){return"--";}function
engines(){return
array();}function
supportsIndex(array$T){return!is_view($T);}function
supportsAlterIndex(array$T){return
true;}function
supportsAlterTable(array$Uj){return
true;}function
indexAlgorithms(array$Uj){return
array();}function
indexOpclasses(){return
array();}function
shadowTables($S){return
array();}function
fulltextSql($C,array$u,$I,$Ua){return"MATCH (".implode(", ",array_map('Adminer\idf_escape',$u["columns"])).") AGAINST (".q($I).($Ua?" IN BOOLEAN MODE":"").")";}function
checkConstraints($S){return
get_key_vals("SELECT c.CONSTRAINT_NAME, CHECK_CLAUSE
FROM INFORMATION_SCHEMA.CHECK_CONSTRAINTS c
JOIN INFORMATION_SCHEMA.TABLE_CONSTRAINTS t
	ON c.CONSTRAINT_SCHEMA = t.CONSTRAINT_SCHEMA AND c.CONSTRAINT_NAME = t.CONSTRAINT_NAME".($this->conn->flavor=='maria'?" AND c.TABLE_NAME = ".q($S):"")."
WHERE c.CONSTRAINT_SCHEMA = ".q($_GET["ns"]!=""?$_GET["ns"]:DB)."
AND t.TABLE_NAME = ".q($S).(JUSH=="pgsql"?"
AND CHECK_CLAUSE NOT LIKE '% IS NOT NULL'":""),$this->conn);}function
allFields(){$K=array();if(DB!=""){foreach(get_rows("SELECT c.TABLE_NAME AS tab, c.COLUMN_NAME AS field, c.IS_NULLABLE AS nullable,
	c.DATA_TYPE AS type, c.CHARACTER_MAXIMUM_LENGTH AS length,
	".(JUSH=='sql'?"c.COLUMN_KEY = 'PRI'":"k.COLUMN_NAME")." AS ".idf_escape("primary")."
FROM INFORMATION_SCHEMA.COLUMNS c".(JUSH=='sql'?"":"
LEFT JOIN INFORMATION_SCHEMA.TABLE_CONSTRAINTS t ON c.TABLE_SCHEMA = t.TABLE_SCHEMA AND c.TABLE_NAME = t.TABLE_NAME AND t.CONSTRAINT_TYPE = 'PRIMARY KEY'
LEFT JOIN INFORMATION_SCHEMA.KEY_COLUMN_USAGE k
	ON t.CONSTRAINT_SCHEMA = k.CONSTRAINT_SCHEMA AND t.CONSTRAINT_NAME = k.CONSTRAINT_NAME AND c.TABLE_SCHEMA = k.TABLE_SCHEMA AND c.TABLE_NAME = k.TABLE_NAME AND c.COLUMN_NAME = k.COLUMN_NAME")."
WHERE c.TABLE_SCHEMA = ".q($_GET["ns"]!=""?$_GET["ns"]:DB)."
ORDER BY c.TABLE_NAME, c.ORDINAL_POSITION",$this->conn)as$L){$L["null"]=($L["nullable"]=="YES");$K[$L["tab"]][]=$L;}}return$K;}}add_driver("sqlite","SQLite");define('Adminer\DRIVER',"sqlite");if(class_exists("SQLite3")&&$_GET["ext"]!="pdo"){abstract
class
SqliteDb
extends
SqlDb{var$extension="SQLite3";private$link;function
attach(array$P,$ol,$G){$this->link=new
\SQLite3($P["path"]);if(method_exists($this->link,'setAuthorizer'))$this->link->setAuthorizer(array($this,'authorize'));$xl=\SQLite3::version();$this->server_info=$xl["versionString"];return'';}function
query($I,$Tk=false){$J=@$this->link->query($I);$this->error="";if(!$J){$this->errno=$this->link->lastErrorCode();$this->error=$this->link->lastErrorMsg();return
false;}elseif($J->numColumns())return
new
Result($J);$this->affected_rows=$this->link->changes();return
true;}function
quote($R){return(is_utf8($R)?"'".$this->link->escapeString($R)."'":"x'".bin2hex($R)."'");}}class
Result{var$num_rows;private$result,$offset=0;function
__construct($J){$this->result=$J;}function
fetch_assoc(){return$this->result->fetchArray(SQLITE3_ASSOC);}function
fetch_row(){return$this->result->fetchArray(SQLITE3_NUM);}function
fetch_field(){$Sk=array(1=>"integer","real","text","blob","null");$d=$this->offset++;return(object)array("name"=>$this->result->columnName($d),"native_type"=>$Sk[$this->result->columnType($d)],);}}}elseif(extension_loaded("pdo_sqlite")){abstract
class
SqliteDb
extends
PdoDb{var$extension="PDO_SQLite";function
attach(array$P,$ol,$G){$K=$this->dsn(DRIVER.":".$P["path"],"","",array(),(class_exists('Pdo\Sqlite')?'Pdo\Sqlite':'PDO'));if(!$K&&method_exists($this->pdo,'setAuthorizer'))$this->pdo->setAuthorizer(array($this,'authorize'));return$K;}function
quote($R){return(is_utf8($R)?parent::quote($R):"x'".bin2hex($R)."'");}}}if(class_exists('Adminer\SqliteDb')){class
Db
extends
SqliteDb{private$attaching=false;function
attach(array$P,$ol,$G){parent::attach($P,$ol,$G);$this->query("PRAGMA foreign_keys = 1");$this->query("PRAGMA busy_timeout = 500");return'';}function
select_db($m){$I="ATTACH ".$this->quote(preg_match("~(^[/\\\\]|:)~",$m)?$m:dirname($_SERVER["SCRIPT_FILENAME"])."/$m")." AS a";$this->attaching=true;$Da=is_readable($m)&&$this->query($I);$this->attaching=false;if($Da)return!self::attach(server_parts(array("path"=>$m)),'','');return
false;}function
authorize($fa,$ya){return($fa!=24||$ya===''||$this->attaching?0:1);}}}class
Driver
extends
SqlDriver{static$extensions=array("SQLite3","PDO_SQLite");static$jush="sqlite";static$passwords=false;static$serverFile=true;protected$types=array(array("integer"=>0,"real"=>0,"numeric"=>0,"text"=>0,"blob"=>0));var$insertFunctions=array();var$editFunctions=array("integer|real|numeric"=>"+/-","text"=>"||",);var$fulltextOperator="MATCH";var$functions=array("hex","length","lower","round","unixepoch","upper");var$grouping=array("avg","count","count distinct","group_concat","max","min","sum");function
operators($Uj){$K=array("=","<",">","<=",">=","!=","LIKE","LIKE %%","IN","IS NULL","NOT LIKE","NOT IN","IS NOT NULL");if(preg_match('~^fts\d+$~i',(string)idx($Uj,"Engine")))$K[]="MATCH";$K[]="SQL";return$K;}static
function
connect($P,$ol,$G){return
parent::connect(":memory:","","");}function
__construct(Db$Ab){parent::__construct($Ab);if(min_version(3.31,0,$Ab))$this->generated=array("STORED","VIRTUAL");if(min_version(3.37,0,$Ab))$this->types[0]["any"]=0;}function
structuredTypes(){return
array_keys($this->types[0]);}function
quoteBinary($Pi){return"x".q(bin2hex($Pi));}function
typeName(\stdClass$k){$K=strtolower(idx((array)$k,'sqlite:decl_type',parent::typeName($k)));return
idx(array("string"=>"text","double"=>"real"),$K,$K);}function
engines(){$K=array("table");if(min_version("3.8.2")){if(min_version(3.37)){$K[]="STRICT";$K[]="STRICT, WITHOUT ROWID";}$K[]="WITHOUT ROWID";}return$K;}private
function
isVirtual(array$T){$Sc=$T["Engine"];return$Sc!=""&&!in_array($Sc,array_merge(array("view"),$this->engines()));}function
supportsIndex(array$T){return!is_view($T)&&!$this->isVirtual($T);}function
supportsAlterIndex(array$T){return$this->supportsIndex($T);}function
supportsAlterTable(array$Uj){return!$this->isVirtual($Uj);}function
shadowTables($S){$K=array();if(min_version(3.37)){foreach(get_vals("SELECT name FROM pragma_table_list WHERE schema = 'main' AND type = 'shadow' ORDER BY name")as$C){if(preg_match('(^'.preg_quote($S).'_[^_]*$)',$C))$K[]=array("table"=>$C,"ns"=>"");}}return$K;}function
fulltextSql($C,array$u,$I,$Ua){return
idf_escape($C)." MATCH ".q($I);}function
insertUpdate($S,array$M,array$fi){$ul=array();foreach($M
as$Q)$ul[]="(".implode(", ",$Q).")";return
queries("REPLACE INTO ".table($S)." (".implode(", ",array_keys(reset($M))).") VALUES\n".implode(",\n",$ul));}function
tableHelp($C,$ef=false){if(preg_match('~^sqlite_(seq|stat.)~',$C,$B))return"fileformat2.html#$B[1]tab";if(preg_match('~^sqlite(_temp)?_(master|schema)$~',$C))return"schematab.html";}function
checkConstraints($S){preg_match_all('~ CHECK *(\( *(((?>[^()]*[^() ])|(?1))*) *\))~',get_val("SELECT sql FROM sqlite_master WHERE type = 'table' AND name = ".q($S),0,$this->conn),$Mf);return
array_combine($Mf[2],$Mf[2]);}function
allFields(){$K=array();if(min_version(3.16)){$M=get_rows('SELECT m.name AS tab, p.name AS field, p.type, p."notnull", p.pk AS '.idf_escape("primary")."
FROM sqlite_master m, pragma_table_".(min_version(3.31)?"x":"")."info(m.name) p
WHERE m.type IN ('table', 'view')".(min_version(3.31)?"
AND p.hidden != 1":"").(min_version(3.37)?"
AND m.name NOT IN (SELECT name FROM pragma_table_list WHERE type = 'shadow')":"")."
ORDER BY (m.name LIKE 'sqlite_%'), m.name, p.cid",$this->conn);foreach($M
as$L){$L["type"]=type_affinity($L["type"]);$L["null"]=!$L["notnull"];$K[$L["tab"]][]=$L;}}else{foreach(tables_list()as$S=>$V){foreach(fields($S)as$k)$K[$S][]=$k;}}return$K;}}function
idf_escape($t){return'"'.str_replace('"','""',$t).'"';}function
table($t){return
idf_escape($t);}function
get_databases($Dd){return
array();}function
limit($I,$Z,$z,$Sg=0,$dj=" "){return" $I$Z".($z?$dj."LIMIT $z".($Sg?" OFFSET $Sg":""):"");}function
limit1($S,$I,$Z,$dj="\n"){return(preg_match('~^INTO~',$I)||get_val("SELECT sqlite_compileoption_used('ENABLE_UPDATE_DELETE_LIMIT')")?limit($I,$Z,1,0,$dj):" $I WHERE rowid = (SELECT rowid FROM ".table($S).$Z.$dj."LIMIT 1)");}function
db_collation($h,array$qb){return
get_val("PRAGMA encoding");}function
logged_user(){return
get_current_user();}function
virtual_module($zj){return(preg_match('~^CREATE\s+VIRTUAL\s+TABLE\s+(?:IF\s+NOT\s+EXISTS\s+)?(?:"[^"]*+"|`[^`]*+`|\[[^\]]*+\]|[^\s(]+)\s+USING\s+([a-z0-9_]+)~i',$zj,$B)?$B[1]:"");}function
tables_list(){return
get_key_vals("SELECT name, type FROM sqlite_master WHERE type IN ('table', 'view')".(min_version(3.37)?" AND name NOT IN (SELECT name FROM pragma_table_list WHERE type = 'shadow')":"")."
ORDER BY (name LIKE 'sqlite_%'), name");}function
count_tables(array$g){return
array();}function
db_status(){$vh=get_val("PRAGMA page_size");$Md=get_val("PRAGMA freelist_count")*$vh;return
array("Data_length"=>get_val("PRAGMA page_count")*$vh-$Md,"Index_length"=>0,"Data_free"=>$Md,);}function
table_status($C="",$pd=false){$K=array();$M=array();if(!$pd&&$C==""){connection()->query("PRAGMA optimize = 0x10002");$M=get_key_vals("SELECT tbl, MAX(CAST(stat AS integer)) FROM sqlite_stat1 GROUP BY tbl");}foreach(get_rows("SELECT name AS Name, type AS Engine, sql, 'rowid' AS Oid, '' AS Auto_increment".(min_version(3.37)?", name IN (SELECT name FROM pragma_table_list WHERE type = 'shadow') AS dependent":"")." FROM sqlite_master WHERE type IN ('table', 'view') ".($C!=""?"AND name = ".q($C):"ORDER BY (name LIKE 'sqlite_%'), name"))as$L){if($L["Engine"]=="table"){$zj=preg_replace('~(?:\s|--[^\n]*|/\*.*?\*/)+$~s','',$L["sql"]);$Nj=preg_replace('~.*\)~s','',$zj);$L["Engine"]=virtual_module($L["sql"])?:(implode(", ",array_filter(array((preg_match('~\bSTRICT\b~i',$Nj)?"STRICT":0),(preg_match('~\bWITHOUT\s+ROWID\b~i',$Nj)?"WITHOUT ROWID":0),)))?:"table");}unset($L["sql"]);$L["Rows"]=idx($M,$L["Name"],0);$K[$L["Name"]]=$L;}if(!$pd){foreach(get_rows("SELECT * FROM sqlite_sequence".($C!=""?" WHERE name = ".q($C):""),null,"")as$L)$K[$L["name"]]["Auto_increment"]=$L["seq"];}return$K;}function
is_view(array$T){return$T["Engine"]=="view";}function
fk_support(array$T){return!get_val("SELECT sqlite_compileoption_used('OMIT_FOREIGN_KEY')");}function
type_affinity($V){$V=strtolower($V);return(preg_match('~int~i',$V)?"integer":(preg_match('~char|clob|text~i',$V)?"text":(preg_match('~blob~i',$V)?"blob":(preg_match('~real|floa|doub~i',$V)?"real":(preg_match('~any~i',$V)?"any":"numeric")))));}function
fields($S){$K=array();$zj=get_val("SELECT sql FROM sqlite_master WHERE type = 'table' AND name = ".q($S));$ji=array("select"=>1,"where"=>1,"order"=>1);if(!preg_match('~^sqlite(_temp)?_(master|schema)$~',$S))$ji+=array("insert"=>1,"update"=>1);$Od=preg_match('~^fts\d+$~i',virtual_module($zj));foreach(get_rows("PRAGMA table_".(min_version(3.31)?"x":"")."info(".table($S).")")as$L){if($L["hidden"]==1)continue;$C=$L["name"];$V=strtolower($L["type"]);$i=$L["dflt_value"];$K[$C]=array("field"=>$C,"type"=>($Od?"text":type_affinity($V)),"full_type"=>$V,"default"=>(preg_match("~^'(.*)'$~",$i,$B)?str_replace("''","'",$B[1]):($i=="NULL"?null:$i)),"null"=>!$L["notnull"],"privileges"=>$ji,"primary"=>$L["pk"],);if($L["pk"]&&preg_match('~\bAUTOINCREMENT\b~i',$zj))$K[$C]["auto_increment"]=true;}$t='[(,]\s*(("[^"]*+")+|[a-z0-9_]+)';$Ei='(?:[^,()\']|\'[^\']*+\'|\([^)]*+\))*?';preg_match_all('~'.$t.'\s+text\b'.$Ei.'COLLATE\s+(\'[^\']+\'|[a-z0-9_]+)~i',$zj,$Mf,PREG_SET_ORDER);foreach($Mf
as$B){$C=str_replace('""','"',preg_replace('~^"|"$~','',$B[1]));if($K[$C])$K[$C]["collation"]=trim($B[3],"'");}preg_match_all('~'.$t.'\s'.$Ei.'GENERATED\s+ALWAYS\s+AS\s*\((.+?)\)\s+(STORED|VIRTUAL)~i',$zj,$Mf,PREG_SET_ORDER);foreach($Mf
as$B){$C=str_replace('""','"',preg_replace('~^"|"$~','',$B[1]));if($K[$C]){$K[$C]["default"]=$B[3];$K[$C]["generated"]=strtoupper($B[4]);}}return$K;}function
indexes($S,$f=null){$f=connection($f);$K=array();$zj=get_val("SELECT sql FROM sqlite_master WHERE type = 'table' AND name = ".q($S),0,$f);if(preg_match('~^fts\d+$~i',virtual_module($zj)))return
array($S=>array("type"=>"FULLTEXT","columns"=>array_keys(fields($S)),"lengths"=>array(),"descs"=>array()));if(preg_match('~\bPRIMARY\s+KEY\s*\((([^)"]+|"[^"]*"|`[^`]*`)++)~i',$zj,$B)){$K[""]=array("type"=>"PRIMARY","columns"=>array(),"lengths"=>array(),"descs"=>array());preg_match_all('~((("[^"]*+")+|(?:`[^`]*+`)+)|(\S+))(\s+(ASC|DESC))?(,\s*|$)~i',$B[1],$Mf,PREG_SET_ORDER);foreach($Mf
as$B){$K[""]["columns"][]=idf_unescape($B[2]).$B[4];$K[""]["descs"][]=(preg_match('~DESC~i',$B[5])?'1':null);}}if(!$K){foreach(fields($S)as$C=>$k){if($k["primary"])$K[""]=array("type"=>"PRIMARY","columns"=>array($C),"lengths"=>array(),"descs"=>array(null));}}$Cj=get_key_vals("SELECT name, sql FROM sqlite_master WHERE type = 'index' AND tbl_name = ".q($S),$f);foreach(get_rows("PRAGMA index_list(".table($S).")",$f)as$L){$C=$L["name"];$u=array("type"=>($L["unique"]?"UNIQUE":"INDEX"));$u["lengths"]=array();$u["descs"]=array();foreach(get_rows("PRAGMA index_info(".idf_escape($C).")",$f)as$Ni){$u["columns"][]=$Ni["name"];$u["descs"][]=null;}if(preg_match('~^CREATE( UNIQUE)? INDEX '.preg_quote(idf_escape($C).' ON '.idf_escape($S),'~').' \((.*)\)$~i',$Cj[$C],$Bi)){preg_match_all('/("[^"]*+")+( DESC)?/',$Bi[2],$Mf);foreach($Mf[2]as$x=>$X){if($X)$u["descs"][$x]='1';}}if(!$K[""]||$u["type"]!="UNIQUE"||$u["columns"]!=$K[""]["columns"]||$u["descs"]!=$K[""]["descs"]||!preg_match("~^sqlite_~",$C))$K[$C]=$u;}return$K;}function
foreign_keys($S){$K=array();foreach(get_rows("PRAGMA foreign_key_list(".table($S).")")as$L){$n=&$K[$L["id"]];if(!$n)$n=$L;$n["source"][]=$L["from"];$n["target"][]=$L["to"];}return$K;}function
view($C){return
array("select"=>preg_replace('~^(?:[^`"[]+|`[^`]*`|"[^"]*")* AS\s+~iU','',get_val("SELECT sql FROM sqlite_master WHERE type = 'view' AND name = ".q($C))));}function
collations(){return(isset($_GET["create"])?get_vals("PRAGMA collation_list",1):array());}function
information_schema($h,$N=""){return
false;}function
error(){return
h(connection()->error);}function
check_sqlite_name($C){$md="db|sdb|sqlite";if(!preg_match("~^[^\\0]*\\.($md)\$~",$C)){connection()->error=lang(29,str_replace("|",", ",$md));return
false;}return
true;}function
create_database($h,$pb){if(file_exists($h)){connection()->error=lang(30);return
false;}if(!check_sqlite_name($h))return
false;try{$_=new
Db();$_->attach(server_parts(array("path"=>$h)),'','');}catch(\Exception$cd){connection()->error=$cd->getMessage();return
false;}$_->query('PRAGMA encoding = "UTF-8"');$_->query('CREATE TABLE adminer (i)');$_->query('DROP TABLE adminer');return
true;}function
drop_databases(array$g){connection()->attach(server_parts(array("path"=>":memory:")),'','');foreach($g
as$h){if(!check_sqlite_name($h))return
false;if(!@unlink($h)){connection()->error=lang(30);return
false;}}return
true;}function
rename_database($C,$pb){if(!check_sqlite_name($C))return
false;connection()->attach(server_parts(array("path"=>":memory:")),'','');connection()->error=lang(30);return@rename(DB,$C);}function
auto_increment(){return" PRIMARY KEY AUTOINCREMENT";}function
alter_table($S,$C,array$l,array$Fd,$ub,$Sc,$pb,$Ga,$Fh){$jl=($S==""||$Fd||$Sc);foreach($l
as$k){if($k[0]!=""||!$k[1]||$k[2]){$jl=true;break;}}$ta=array();$qh=array();foreach($l
as$k){if($k[1]){$ta[]=($jl?$k[1]:"ADD ".implode($k[1]));if($k[0]!="")$qh[$k[0]]=$k[1][0];}}if(!$jl){foreach($ta
as$X){if(!queries("ALTER TABLE ".table($S)." $X"))return
false;}if($S!=$C&&!queries("ALTER TABLE ".table($S)." RENAME TO ".table($C)))return
false;}elseif(!recreate_table($S,$C,$ta,$qh,$Fd,$Ga,array(),"","",$Sc))return
false;if($Ga){queries("BEGIN");queries("UPDATE sqlite_sequence SET seq = $Ga WHERE name = ".q($C));if(!connection()->affected_rows)queries("INSERT INTO sqlite_sequence (name, seq) VALUES (".q($C).", $Ga)");queries("COMMIT");}return
true;}function
recreate_table($S,$C,array$l,array$qh,array$Fd,$Ga="",$v=array(),$Dc="",$ha="",$Sc=""){if($S!=""){if(!$l){foreach(fields($S)as$x=>$k){if($v)$k["auto_increment"]=0;$l[]=process_field($k,$k);$qh[$x]=idf_escape($x);}}$gi=false;foreach($l
as$k){if($k[6])$gi=true;}$Fc=array();foreach($v
as$x=>$X){if($X[2]=="DROP"){$Fc[$X[1]]=true;unset($v[$x]);}}foreach(indexes($S)as$kf=>$u){$e=array();foreach($u["columns"]as$x=>$d){if(!$qh[$d])continue
2;$e[]=$qh[$d].($u["descs"][$x]?" DESC":"");}if(!$Fc[$kf]){if($u["type"]!="PRIMARY"||!$gi)$v[]=array($u["type"],$kf,$e);}}foreach($v
as$x=>$X){if($X[0]=="PRIMARY"){unset($v[$x]);$Fd[]="  PRIMARY KEY (".implode(", ",$X[2]).")";}}foreach(foreign_keys($S)as$kf=>$n){foreach($n["source"]as$x=>$d){if(!$qh[$d])continue
2;$n["source"][$x]=idf_unescape($qh[$d]);}if(!isset($Fd[" $kf"]))$Fd[]=" ".format_foreign_key($n);}queries("BEGIN");}$ab=array();foreach($l
as$k){if(preg_match('~GENERATED~',$k[3]))unset($qh[array_search($k[0],$qh)]);$ab[]="  ".implode($k);}$ab=array_merge($ab,array_filter($Fd));foreach(driver()->checkConstraints($S)as$eb){if($eb!=$Dc)$ab[]="  CHECK ($eb)";}if($ha)$ab[]="  CHECK ($ha)";$nk=($S!=""&&$S==$C?"adminer_$C":$C);if(!$Sc&&$S!="")$Sc=idx(table_status1($S),"Engine");if(!queries("CREATE TABLE ".table($nk)." (\n".implode(",\n",$ab)."\n)".($Sc!="table"&&in_array($Sc,driver()->engines())?" $Sc":"")))return
false;if($S!=""){if($qh&&!queries("INSERT INTO ".table($nk)." (".implode(", ",$qh).") SELECT ".implode(", ",array_map('Adminer\idf_escape',array_keys($qh)))." FROM ".table($S)))return
false;$Mk=array();foreach(triggers($S)as$Kk=>$uk){$Ik=trigger($Kk,$S);$Mk[]="CREATE TRIGGER ".idf_escape($Kk)." ".implode(" ",$uk)." ON ".table($C)."\n$Ik[Statement]";}$Ga=$Ga?"":get_val("SELECT seq FROM sqlite_sequence WHERE name = ".q($S));if(!queries("DROP TABLE ".table($S))||($S==$C&&!queries("ALTER TABLE ".table($nk)." RENAME TO ".table($C)))||!alter_indexes($C,$v))return
false;if($Ga)queries("UPDATE sqlite_sequence SET seq = $Ga WHERE name = ".q($C));foreach($Mk
as$Ik){if(!queries($Ik))return
false;}queries("COMMIT");}return
true;}function
index_sql($S,$V,$C,$e){return"CREATE $V ".($V!="INDEX"?"INDEX ":"").idf_escape($C!=""?$C:uniqid($S."_"))." ON ".table($S)." $e";}function
alter_indexes($S,$ta){foreach($ta
as$fi){if($fi[0]=="PRIMARY")return
recreate_table($S,$S,array(),array(),array(),"",$ta);}foreach(array_reverse($ta)as$X){if(!queries($X[2]=="DROP"?"DROP INDEX ".idf_escape($X[1]):index_sql($S,$X[0],$X[1],"(".implode(", ",$X[2]).")")))return
false;}return
true;}function
truncate_tables(array$U){return
apply_queries("DELETE FROM",$U);}function
drop_views(array$zl){return
apply_queries("DROP VIEW",$zl);}function
drop_tables(array$U){return
apply_queries("DROP TABLE",$U);}function
move_tables(array$U,array$zl,$jk){return
false;}function
trigger($C,$S){if($C=="")return
array("Statement"=>"BEGIN\n\t;\nEND");$t='(?:[^`"\s]+|`[^`]*`|"[^"]*")+';$Lk=trigger_options();preg_match("~^CREATE\\s+TRIGGER\\s*$t\\s*(".implode("|",$Lk["Timing"]).")\\s+([a-z]+)(?:\\s+OF\\s+($t))?\\s+ON\\s*$t\\s*(?:FOR\\s+EACH\\s+ROW\\s)?(.*)~is",get_val("SELECT sql FROM sqlite_master WHERE type = 'trigger' AND name = ".q($C)),$B);if(!$B)return
array();$Ng=$B[3];return
array("Timing"=>strtoupper($B[1]),"Event"=>strtoupper($B[2]).($Ng?" OF":""),"Of"=>idf_unescape($Ng),"Trigger"=>$C,"Statement"=>$B[4],);}function
triggers($S){$K=array();$Lk=trigger_options();foreach(get_rows("SELECT * FROM sqlite_master WHERE type = 'trigger' AND tbl_name = ".q($S))as$L){preg_match('~^CREATE\s+TRIGGER\s*(?:[^`"\s]+|`[^`]*`|"[^"]*")+\s*('.implode("|",$Lk["Timing"]).')\s*(.*?)\s+ON\b~i',$L["sql"],$B);$K[$L["name"]]=array($B[1],$B[2]);}return$K;}function
trigger_options(){return
array("Timing"=>array("BEFORE","AFTER","INSTEAD OF"),"Event"=>array("INSERT","UPDATE","UPDATE OF","DELETE"),"Type"=>array("FOR EACH ROW"),);}function
last_id($J){return
get_val("SELECT LAST_INSERT_ROWID()");}function
explain(Db$Ab,$I){return$Ab->query("EXPLAIN QUERY PLAN $I");}function
found_rows(array$T,array$Z){}function
types($md=false){return
array();}function
create_sql($S,$Ga,$Lj){$K=get_val("SELECT sql FROM sqlite_master WHERE type IN ('table', 'view') AND name = ".q($S));foreach(indexes($S)as$C=>$u){if($C==''||$u['type']=='FULLTEXT')continue;$K
.=";\n\n".index_sql($S,$u['type'],$C,"(".implode(", ",array_map('Adminer\idf_escape',$u['columns'])).")");}return$K;}function
truncate_sql($S){return"DELETE FROM ".table($S);}function
use_sql($Xb,$Lj=""){return"";}function
trigger_sql($S){return
implode(get_vals("SELECT sql || ';;\n' FROM sqlite_master WHERE type = 'trigger' AND tbl_name = ".q($S)));}function
show_variables(){$K=array();foreach(get_rows("PRAGMA pragma_list")as$L){$C=$L["name"];if($C!="pragma_list"&&$C!="compile_options"){$K[$C]=array($C,'');foreach(get_rows("PRAGMA $C")as$L)$K[$C][1].=implode(", ",$L)."\n";}}return$K;}function
show_status(){$K=array();foreach(get_vals("PRAGMA compile_options")as$dh)$K[]=explode("=",$dh,2)+array('','');return$K;}function
convert_field(array$k){}function
unconvert_field(array$k,$K){return$K;}function
support($qd){return
preg_match('~^(check|columns|database|drop_col|dump|fast_status|indexes|descidx|move_col|sql|status|table|transaction_ddl|trigger|variables|view|view_trigger)$~',$qd);}class
Adminer{static$instance;var$error='';function
name(){return"<a href='https://www.adminer.org/'".target_blank()." id='h1'><img src='".h(preg_replace("~\\?.*~","",ME)."?file=logo.svg&version=6.1.1+5f8c6c15")."' width='24' height='24' alt='' id='logo'>Adminer</a>";}function
credentials(){return
array(SERVER,$_GET["username"],get_password());}function
connectSsl(){}function
permanentLogin($Lb=false){return
password_file($Lb);}function
bruteForceKey(){return$_SERVER["REMOTE_ADDR"];}function
verifyLoginToken(){return
true;}function
serverName($P){return
h($P);}function
database(){return
DB;}function
databases($Dd=true){return
get_databases($Dd);}function
pluginsLinks(){}function
operators($Uj=null){return
driver()->operators($Uj);}function
schemas(){$K=schemas();if($_GET["ns"]!=""&&!in_array($_GET["ns"],$K))array_unshift($K,$_GET["ns"]);return$K;}function
queryTimeout(){return
2;}function
afterConnect(){}function
headers(){}function
csp(array$Ob){return$Ob;}function
verifyVersion(){return
true;}function
serviceWorker(){service_worker();}function
manifest(){$ve=$_SERVER["HTTP_HOST"]?:$_SERVER["SERVER_NAME"];$bj=preg_replace('~\?.*~','',ME)?:'.';return
array('name'=>"Adminer".($ve!=""?" - $ve":""),'short_name'=>'Adminer','description'=>lang(31),'start_url'=>$bj,'scope'=>$bj,'display'=>'minimal-ui','icons'=>array(array('src'=>preg_replace("~\\?.*~","",ME)."?file=logo.svg&version=6.1.1+5f8c6c15",'sizes'=>'any','type'=>'image/svg+xml')),);}function
head($Tb=null){return
true;}function
bodyClass(){echo" adminer";}function
css(){$K=array();foreach(array("","-dark")as$qg){$m="adminer$qg.css";if(file_exists($m)){$vd=file_get_contents($m);$K["$m?v=".crc32($vd)]=($qg?"dark":(preg_match('~prefers-color-scheme:\s*dark~',$vd)?'':'light'));}}return$K;}function
loginForm(){echo"<table class='layout'>\n",adminer()->loginFormField('driver','<tr><th>'.lang(32).'<td>',input_hidden("auth[driver]","sqlite")."SQLite"),input_hidden("auth[server]",SERVER),adminer()->loginFormField('username','<tr><th>'.lang(33).'<td>','<input name="auth[username]" id="username" autofocus value="'.h($_GET["username"]).'" autocomplete="username" autocapitalize="off">'),adminer()->loginFormField('password','<tr><th>'.lang(34).'<td>','<input type="password" name="auth[password]" autocomplete="current-password">'),adminer()->loginFormField('db','<tr><th>'.lang(35).'<td>','<input name="auth[db]" value="'.h($_GET["db"]).'" autocapitalize="off">'),"</table>\n","<p><input type='submit' value='".lang(36)."'>\n",checkbox("auth[permanent]",1,$_COOKIE["adminer_permanent"],lang(37))."\n";}function
loginFormField($C,$me,$Y){return$me.$Y."\n";}function
login($Jf,$G){if($G=="")return
lang(38).require_password_link(null);if(!Driver::$passwords)return
lang(39).require_password_link($G);if(!password_required())return
lang(40).require_password_link($G);return
true;}function
tableName(array$Uj){return
h($Uj["Name"]);}function
fieldName(array$k,$E=0){$V=$k["full_type"].($k["null"]?" NULL":"");$ub=$k["comment"];return'<span title="'.h($V.($ub!=""?($V?": ":"").$ub:'')).'">'.h($k["field"]).'</span>';}function
commentValue($V,$ub){if($ub==""||$V=='TABLE'||$V=='COLUMN')return
h($ub);$ai=function($Pi,$Ya='td'){return
preg_replace('~^~m','<tr>',preg_replace('~\|~',"<$Ya>",preg_replace('~\|$~m',"",rtrim($Pi))));};$S='(\+--[-+]+\+\n)';$L='(\| .* \|\n)';return"<pre>\n".preg_replace_callback("~^$S?$L$S?($L*)$S?~m",function($B)use($ai){return"<table>\n".($B[1]?"<thead>".$ai($B[2],'th')."<tbody>\n":$ai($B[2])).$ai($B[4])."\n</table>";},preg_replace('~(\n(    -|mysql)&gt; )(.+)~',"\\1<code class='jush-sql'>\\3</code>",preg_replace('~(.+)\n---+\n~',"<b>\\1</b>\n",h($ub))))."</pre>\n";}function
commentInput($V,$b,$ub){$Y=h($ub);return(preg_match('~\n~',$Y)?"<textarea$b rows='2' cols='".($V=='TABLE'?20:30)."' style='vertical-align: bottom;'>\n$Y</textarea>":"<input$b value='$Y'>");}function
selectLinks(array$Uj,$Q=""){$C=$Uj["Name"];echo'<p class="links">';$Ff=array();if($C!="")$Ff["select"]=lang(41);if(support("table")||support("indexes"))$Ff["table"]=lang(42);if(support("table")){if(is_view($Uj)){if(support("view"))$Ff["view"]=lang(43);}elseif(function_exists('Adminer\alter_table')&&$C!="")$Ff["create"]=lang(44);}if($Q!==null)$Ff["edit"]=lang(45);foreach($Ff
as$x=>$X)echo" <a href='".h(ME)."$x=".url_escape($C).($x=="edit"?$Q:"")."'".bold(isset($_GET[$x])).">$X</a>";echo"\n";}function
foreignKeys($S){return
foreign_keys($S);}function
backwardKeys($S,$Tj){return
array();}function
backwardKeysPrint(array$La,array$L){}function
selectQuery($I,$Dj,$od=false){$K="\n";if(!$od&&($Bl=driver()->warnings())){$s="warnings";$K=", <a href='#$s' class='toggle'>".lang(46)."</a>"."$K<div id='$s' class='hidden'>\n$Bl</div>\n";}return"<p><code class='jush-".JUSH."'>".h(str_replace("\n"," ",$I))."</code> <span class='time'>(".format_time($Dj).")</span>".(support("sql")?" <a href='".h(ME)."sql=".url_escape($I)."' class='hover'>".lang(14)."</a>":"").$K;}function
sqlCommandQuery($I){return
shorten_utf8(trim($I),1000);}function
sqlPrintAfter(){}function
explain(Db$Ab,$I,array$jh){$J=explain($Ab,$I);if(!$J)return"";ob_start();print_select_result($J,$Ab,$jh);return
ob_get_clean();}function
rowDescription($S){return"";}function
rowDescriptions(array$M,array$Gd){return$M;}function
selectLink($X,array$k){}function
selectVal($X,$_,array$k,$ph){$K=($X===null?"<i>NULL</i>":(preg_match("~char|binary|boolean~",$k["type"])&&!preg_match("~var~",$k["type"])?"<code>$X</code>":(preg_match('~^jsonb?$~',$k["full_type"])?"<code class='jush-json'>$X</code>":$X)));if(is_blob($k)&&!is_utf8($X))$K="<i>".lang(47,strlen($ph))."</i>";return($_?"<a href='".h($_)."'".(is_url($_)?target_blank():"").">$K</a>":$K);}function
editVal($X,array$k){return$X;}function
config(){return
array();}function
tableStructurePrint(array$l,$Uj=null){echo"<div class='scrollable'>\n","<table class='nowrap odds'>\n","<thead><tr><th>".lang(48)."<th>".lang(49).(support("comment")?"<th>".lang(50):"")."<tbody>\n";$nl=(support("type")?types():array());foreach($l
as$k){echo"<tr><th>".h($k["field"]);$V=h($k["full_type"]);$pb=h($k["collation"]);echo"<td><span title='$pb'>".(in_array($V,$nl)?"<a href='".h(ME.'type='.url_escape($V))."'>$V</a>":$V.($pb&&isset($Uj["Collation"])&&$pb!=$Uj["Collation"]?" $pb":""))."</span>",($k["null"]?" <i>NULL</i>":""),($k["auto_increment"]?" <i>".lang(51)."</i>":""),(isset($k["default"])?" <span title='".lang(52)."'>[<b>".($k["generated"]?"<code class='jush-".JUSH."'>".shorten_utf8(preg_replace('~\s+~',' ',ltrim($k["default"])),80,"</code>"):h($k["default"]))."</b>]</span>":""),(support("comment")?"<td>".adminer()->commentValue('COLUMN',$k["comment"]):""),"\n";}echo"</table>\n","</div>\n";}function
tableIndexesPrint(array$v,array$Uj){$Ah=false;foreach($v
as$C=>$u)$Ah|=!!$u["partial"];echo"<table>\n";$dc=first(driver()->indexAlgorithms($Uj));foreach($v
as$C=>$u){ksort($u["columns"]);$hi=array();foreach($u["columns"]as$x=>$X)$hi[]="<i>".h($X)."</i>".($u["lengths"][$x]?"(".h($u["lengths"][$x]).")":"").($u["descs"][$x]?" DESC":"");echo"<tr title='".h($C)."'>","<th>".h($u["type"]).($dc&&$u['algorithm']!=$dc?" (".h($u['algorithm']).")":""),"<td>".implode(", ",$hi);if($Ah)echo"<td>".($u['partial']?"<code class='jush-".JUSH."'>WHERE ".h($u['partial']):"");echo"\n";}echo"</table>\n";}function
namePattern($V){if($V=="FOREIGN"||$V=="CHECK")return"";if($V=="TRIGGER")return"{table}_{timing}{event}";return(JUSH=="sql"?"":"{table}_")."{columns}";}function
selectColumnsPrint(array$O,array$e){print_fieldset("select",lang(53),$O);$r=0;$O[""]=array();foreach($O
as$x=>$X){$X=idx($_GET["columns"],$x,array());$d=select_input(" name='columns[$r][col]' data-default=''".on('change',($x!==""?'selectFieldChange':'selectAddRow')),$e,$X["col"]);echo"<div>".(driver()->functions||driver()->grouping?html_select("columns[$r][fun]",array(-1=>"")+array_filter(array(lang(54)=>driver()->functions,lang(55)=>driver()->grouping)),$X["fun"]," data-default=''".on('change',($x!==""?'helpClose':'selectFunAddRow')).on_help_value(' (.*)|$','($1)'))."($d)":$d)."</div>\n";$r++;}echo"</div></fieldset>\n";}function
selectSearchPrint(array$Z,array$e,array$v,$Uj=null){print_fieldset("search",lang(56),$Z);foreach($v
as$r=>$u){if($u["type"]=="FULLTEXT")echo"<div>(<i>".implode("</i>, <i>",array_map('Adminer\h',$u["columns"]))."</i>) ".h(driver()->fulltextOperator)," <input type='search' name='fulltext[$r]' value='".h(idx($_GET["fulltext"],$r))."' data-default=''".on('input','selectFieldChange').">",(JUSH=='sql'?checkbox("boolean[$r]",1,isset($_GET["boolean"][$r]),"BOOL"):''),"</div>\n";}$bh=adminer()->operators($Uj);foreach(array_merge((array)$_GET["where"],array(array()))as$r=>$X){if(!$X||(("$X[col]$X[val]"!=""||preg_match('~NULL$~',$X["op"]))&&in_array($X["op"],$bh)))echo"<div>".select_input(" name='where[$r][col]' data-default=''".on('change',($X?'selectFieldChange':'selectAddRow')),$e,$X["col"],"(".lang(57).")"),html_select("where[$r][op]",$bh,$X["op"]," data-default='".h(first($bh))."'".on('change','selectFirstChange')),"<input type='search' name='where[$r][val]' value='".h($X["val"])."' data-default=''".on('input','selectFirstChange').on('keydown','selectSearchKeydown').on('search','selectSearchSearch').">","</div>\n";}echo"</div></fieldset>\n";}function
selectOrderPrint(array$E,array$e,array$v){print_fieldset("sort",lang(58),$E);$r=0;foreach((array)$_GET["order"]as$x=>$X){if($X!=""){echo"<div>".select_input(" name='order[$r]' data-default=''".on('change','selectFieldChange'),$e,$X),checkbox("desc[$r]",1,isset($_GET["desc"][$x]),lang(59))."</div>\n";$r++;}}echo"<div>".select_input(" name='order[$r]' data-default=''".on('change','selectAddRow'),$e),checkbox("desc[$r]",1,false,lang(59))."</div>\n","</div></fieldset>\n";}function
selectLimitPrint($z){echo"<fieldset><legend>".lang(60)."</legend><div>","<input type='number' name='limit' class='size' value='".h($z?:"")."' data-default='50'".on('input','selectFieldChange').">","</div></fieldset>\n";}function
selectLengthPrint($qk){echo"<fieldset><legend>".lang(61)."</legend><div>","<input type='number' name='text_length' class='size' value='".h($qk)."' data-default='100'>","</div></fieldset>\n";}function
selectActionPrint(array$v){echo"<fieldset><legend>".lang(62)."</legend><div>","<input type='submit' value='".lang(53)."'>"," <span id='noindex' title='".lang(63)."'></span>","<script".nonce().">\n","const indexColumns = ";$e=array();foreach($v
as$u){$Sb=reset($u["columns"]);if($u["type"]!="FULLTEXT"&&$Sb)$e[$Sb]=1;}$e[""]=1;foreach($e
as$x=>$X)json_row($x);echo";\n","selectFieldChange.call(qs('#form')['select']);\n","</script>\n","</div></fieldset>\n";}function
selectCommandPrint(){return!information_schema(DB);}function
selectImportPrint(){return!information_schema(DB);}function
selectEmailPrint(array$Oc,array$e){}function
selectColumnsProcess(array$e,array$v){$O=array();$q=array();foreach((array)$_GET["columns"]as$x=>$X){if($X["fun"]=="count"||($X["col"]!=""&&(!$X["fun"]||in_array($X["fun"],driver()->functions)||in_array($X["fun"],driver()->grouping)))){$O[$x]=apply_sql_function($X["fun"],($X["col"]!=""?idf_escape($X["col"]):"*"));if(!in_array($X["fun"],driver()->grouping))$q[]=$O[$x];}}return
array($O,$q);}function
selectSearchProcess(array$l,array$v,$Uj=null){$K=array();foreach($v
as$r=>$u){if($u["type"]=="FULLTEXT"&&idx($_GET["fulltext"],$r)!="")$K[]=driver()->fulltextSql($r,$u,$_GET["fulltext"][$r],isset($_GET["boolean"][$r]));}$bh=adminer()->operators($Uj);foreach((array)$_GET["where"]as$x=>$X){$X+=array("col"=>"","op"=>first($bh),"val"=>"");$_GET["where"][$x]=$X;$c=$X["col"];if(("$c$X[val]"!=""||preg_match('~NULL$~',$X["op"]))&&in_array($X["op"],$bh)){if($X["op"]=="SQL"&&(!$_POST||!verify_token()))SqlDb::$untrusted=true;$zb=array();foreach(($c!=""?array($c=>$l[$c]):$l)as$C=>$k){$ci="";$yb=" $X[op]";if(preg_match('~IN$~',$X["op"]))$yb
.=" ".($X["val"]!=""?process_in($X["val"]):"(NULL)");elseif($X["op"]=="SQL")$yb=" $X[val]";elseif(preg_match('~^(I?LIKE) %%$~',$X["op"],$B))$yb=" $B[1] ".q("%$X[val]%");elseif($X["op"]=="FIND_IN_SET"){$ci="$X[op](".q($X["val"]).", ";$yb=")";}elseif(!preg_match('~NULL$~',$X["op"]))$yb
.=" ".q($X["val"]);if($c!=""||is_searchable($k,$X))$zb[]=$ci.driver()->convertSearch(idf_escape($C),$X,$k).$yb;}$K[]=(count($zb)==1?$zb[0]:($zb?"(".implode(" OR ",$zb).")":"1 = 0"));}}return$K;}function
selectOrderProcess(array$l,array$v){$K=array();foreach((array)$_GET["order"]as$x=>$X){if($X!="")$K[]=(preg_match('~^((COUNT\(DISTINCT |[A-Z0-9_]+\()(`(?:[^`]|``)+`|"(?:[^"]|"")+")\)|COUNT\(\*\))$~',$X)?$X:idf_escape($X)).(isset($_GET["desc"][$x])?" DESC".(JUSH=='pgsql'&&idx($l[$X],"null")?" NULLS LAST":""):"");}return$K;}function
selectLimitProcess(){return(isset($_GET["limit"])?intval($_GET["limit"]):50);}function
selectLengthProcess(){return(isset($_GET["text_length"])?"$_GET[text_length]":"100");}function
selectEmailProcess(array$Z,array$Gd){return
false;}function
selectQueryBuild(array$O,array$Z,array$q,array$E,$z,$F){return"";}function
messageQuery($I,$sk,$od=false){restart_session();$se=&get_session("queries");if(!idx($se,$_GET["db"]))$se[$_GET["db"]]=array();if(strlen($I)>1e6)$I=preg_replace('~[\x80-\xFF]+$~','',substr($I,0,1e6))."\n…";$se[$_GET["db"]][]=array($I,time(),$sk);$Aj="sql-".count($se[$_GET["db"]]);$K="<a href='#$Aj' class='toggle'>".lang(64)."</a> ".copy_icon()."\n";if(!$od&&($Bl=driver()->warnings())){$s="warnings-".count($se[$_GET["db"]]);$K="<a href='#$s' class='toggle'>".lang(46)."</a>, $K<div id='$s' class='hidden'>\n$Bl</div>\n";}return" <span class='time'>".@date("H:i:s")."</span>"." $K<div id='$Aj' class='hidden'><pre><code class='jush-".JUSH."'>".shorten_utf8($I,1e4)."</code></pre>".($sk?" <span class='time'>($sk)</span>":'').(support("sql")?'<p><a href="'.h(str_replace("db=".url_escape(DB),"db=".url_escape($_GET["db"]),ME).'sql=&history='.(count($se[$_GET["db"]])-1)).'">'.lang(14).'</a>':'').'</div>';}function
error(){return
error();}function
editRowPrint($S,array$l,$L,$cl,$I='',$sk=''){echo($I!=""?"<p><code class='jush-".JUSH."'>".h(str_replace("\n"," ",$I))."</code> <span class='time'>($sk)</span>\n":"");}function
editFunctions(array$k){$K=($k["null"]?"NULL/":"");$ie=isset($_GET["select"])||where($_GET);foreach(array(driver()->insertFunctions,driver()->editFunctions)as$x=>$Sd){if(!$x||(!isset($_GET["call"])&&$ie)){foreach($Sd
as$Mh=>$X){if(!$Mh||preg_match("~$Mh~",$k["type"]))$K
.="/$X";}}if($x&&$Sd&&!preg_match('~set|bool~',$k["type"])&&!is_blob($k))$K
.="/SQL";}if($k["auto_increment"]&&!$ie)$K=lang(51);return
explode("/",$K);}function
editInput($S,array$k,$b,$Y){if($k["type"]=="enum")return(isset($_GET["select"])?"<label><input type='radio'$b value='orig' checked><i>".lang(12)."</i></label> ":"").enum_input("radio",$b,$k,$Y,"NULL");return"";}function
editHint($S,array$k,$Y){return"";}function
processInput(array$k,$Y,$p=""){if($p=="SQL")return$Y;$C=$k["field"];$K=q($Y);if(preg_match('~^(now|getdate|uuid)$~',$p))$K="$p()";elseif(preg_match('~^current_(date|timestamp)$~',$p))$K=$p;elseif(preg_match('~^([+-]|\|\|)$~',$p))$K=idf_escape($C)." $p $K";elseif(preg_match('~^[+-] interval$~',$p))$K=idf_escape($C)." $p ".(preg_match("~^(\\d+|'[0-9.: -]') [A-Z_]+\$~i",$Y)&&JUSH!="pgsql"?$Y:$K);elseif(preg_match('~^(addtime|subtime|concat)$~',$p))$K="$p(".idf_escape($C).", $K)";elseif(preg_match('~^(md5|sha1|password|encrypt)$~',$p))$K="$p($K)";return
unconvert_field($k,$K);}function
dumpOutput(){$K=array('text'=>lang(65),'file'=>lang(66));if(function_exists('gzencode'))$K['gz']='gzip';return$K;}function
dumpFormat(){return(support("dump")?array('sql'=>'SQL'):array())+array('csv'=>'CSV,','csv;'=>'CSV;','tsv'=>'TSV');}function
dumpPrint(){}function
dumpDatabase($h){}function
dumpTable($S,$Lj,$ef=0){if($_POST["format"]!="sql"){echo"\xef\xbb\xbf";if($Lj)dump_csv(array_keys(fields($S)));}else{if($ef==2){$l=array();foreach(fields($S)as$C=>$k)$l[]=idf_escape($C)." ".full_type_sql($k);$Lb="CREATE TABLE ".table($S)." (".implode(", ",$l).")";}else$Lb=create_sql($S,$_POST["auto_increment"],$Lj);set_utf8mb4($Lb);if($Lj&&$Lb){if(($Lj=="DROP+CREATE"&&!function_exists('Adminer\drop_sql'))||$ef==1)echo"DROP ".($ef==2?"VIEW":"TABLE")." IF EXISTS ".table($S).";\n";if($ef==1)$Lb=remove_definer($Lb);echo"$Lb;\n\n";}}}function
dumpData($S,$Lj,$I,array$O=array(),array$Z=array(),array$q=array(),array$E=array()){if($Lj){$Sf=(JUSH=="sqlite"?0:1048576);$l=array();$_e=false;if($_POST["format"]=="sql"){if($Lj=="TRUNCATE+INSERT"&&!function_exists('Adminer\truncate_all_sql'))echo
truncate_sql($S).";\n";$l=fields($S);if(JUSH=="mssql"){foreach($l
as$k){if($k["auto_increment"]){echo"SET IDENTITY_INSERT ".table($S)." ON;\n";$_e=true;break;}}}}$J=($I!=""?connection()->query($I,1):driver()->select($S,($O?:array("*")),$Z,$q,$E,0));if($J){$Re="";$Wa="";$lf=array();$Td=array();$Nj="";$rd=($S!=''?'fetch_assoc':'fetch_row');$Kb=0;while($L=$J->$rd()){if(!$lf){$ul=array();foreach($L
as$X){$k=$J->fetch_field();if(idx($l[$k->name],'generated')){$Td[$k->name]=true;continue;}$lf[]=$k->name;$x=idf_escape($k->name);$ul[]="$x = VALUES($x)";}$Nj=($Lj=="INSERT+UPDATE"?"\nON DUPLICATE KEY UPDATE ".implode(", ",$ul):"").";\n";}if($_POST["format"]!="sql"){if($Lj=="table"){dump_csv($lf);$Lj="INSERT";}dump_csv($L);}else{if(!$Re)$Re="INSERT INTO ".table($S)." (".implode(", ",array_map('Adminer\idf_escape',$lf)).") VALUES";foreach($L
as$x=>$X){if($Td[$x]){unset($L[$x]);continue;}$k=$l[$x];$L[$x]=($X===null?"NULL":($X===false?0:unconvert_field($k,preg_match(number_type(),$k["type"])&&!preg_match('~\[~',$k["full_type"])&&is_numeric($X)?$X:(!is_blob($k)||is_utf8($X)?q($X):driver()->quoteBinary($X)))));}$Pi=($Sf?"\n":" ")."(".implode(",\t",$L).")";if(!$Wa)$Wa=$Re.$Pi;elseif(JUSH=='mssql'?$Kb%1000!=0:strlen($Wa)+4+strlen($Pi)+strlen($Nj)<$Sf)$Wa
.=",$Pi";else{echo$Wa.$Nj;$Wa=$Re.$Pi;}}$Kb++;}if($Wa)echo$Wa.$Nj;}elseif($_POST["format"]=="sql")echo"-- ".str_replace("\n"," ",connection()->error)."\n";if($_e)echo"SET IDENTITY_INSERT ".table($S)." OFF;\n";}}function
dumpFilename($ze){return
friendly_url($ze!=""?$ze:(SERVER?:"localhost"));}function
dumpHeaders($ze,$vg=false){$th=$_POST["output"];$kd=(preg_match('~sql~',$_POST["format"])?"sql":($vg?"tar":"csv"));header("Content-Type: ".($th=="gz"?"application/x-gzip":($kd=="tar"?"application/x-tar":($kd=="sql"||$th!="file"?"text/plain":"text/csv")."; charset=utf-8")));if($th=="gz"){ob_start(function($R){return
gzencode($R);},1e6);}return$kd;}function
dumpFooter(){if($_POST["format"]=="sql")echo"-- ".gmdate("Y-m-d H:i:s e")."\n";}function
importServerPath(){return"adminer.sql";}function
importPrint(){}function
importProcess(){return
false;}function
homepage(){echo'<p class="links">'.($_GET["ns"]==""&&support("database")?'<a href="'.h(ME).'database=">'.lang(67)."</a>\n":""),(support("scheme")?"<a href='".h(ME)."scheme='>".($_GET["ns"]!=""?lang(68):lang(69))."</a>\n":""),($_GET["ns"]!==""?'<a href="'.h(ME).'schema=">'.lang(70)."</a>\n":""),(support("privileges")?"<a href='".h(ME)."privileges='>".lang(71)."</a>\n":"");if($_GET["ns"]!=="")echo(support("routine")?"<a href='#routines'>".lang(72)."</a>\n":""),(support("sequence")?"<a href='#sequences'>".lang(73)."</a>\n":""),(support("type")?"<a href='#user-types'>".lang(0)."</a>\n":""),(support("event")?"<a href='#events'>".lang(74)."</a>\n":"");return
true;}function
navigation($pg){echo"<h1>".adminer()->name()." <span class='version'>".VERSION;$Gg=$_COOKIE["adminer_version"];echo" <a href='https://www.adminer.org/#download'".target_blank()." id='version'>".(version_compare(VERSION,$Gg)<0?h($Gg):"").version_iframe()."</a>","</span></h1>\n";switch_lang();if($pg=="auth"){$th="";foreach((array)$_SESSION["pwds"]as$wl=>$kj){foreach($kj
as$P=>$pl){$C=h(get_setting("vendor-$wl-$P")?:get_driver($wl));foreach($pl
as$ol=>$G){if($C&&$G!==null){$bc=$_SESSION["db"][$wl][$P][$ol];foreach(($bc?array_keys($bc):array(""))as$h)$th
.="<li><a href='".h(auth_url($wl,$P,$ol,$h))."'>($C) ".h("$ol@").($P!=""?adminer()->serverName($P):"").h($h!=""?" - $h":"")."</a>\n";}}}}if($th)echo"<ul id='logins'".on('mouseover','menuOver').on('mouseout','menuOut').">\n$th</ul>\n";}else{$U=array();if($_GET["ns"]!==""&&!$pg&&DB!=""){connection()->select_db(DB);$U=table_status('',true);}adminer()->syntaxHighlighting($U);adminer()->databasesPrint($pg);$ga=array();if(DB==""||!$pg){if(support("sql")){$ga['sql']="<a href='".h(ME)."sql='".bold(isset($_GET["sql"])&&!isset($_GET["import"])).">".lang(64)."</a>";$ga['import']="<a href='".h(ME)."import='".bold(isset($_GET["import"])).">".lang(75)."</a>";}$ga['dump']="<a href='".h(ME)."dump=".url_escape(isset($_GET["table"])?$_GET["table"]:$_GET["select"])."' id='dump'".bold(isset($_GET["dump"])).">".lang(76)."</a>";}$Ee=$_GET["ns"]!==""&&!$pg&&DB!="";if($Ee&&function_exists('Adminer\alter_table'))$ga['create']='<a href="'.h(ME).'create="'.bold($_GET["create"]==="").">".lang(77)."</a>";$ga=adminer()->menuActions($ga,$pg);echo($ga?"<p class='links'>\n".implode("\n",$ga)."\n":"");if($Ee){if($U)adminer()->tablesPrint($U);else
echo"<p class='message'>".lang(13)."</p>\n";}}}function
syntaxHighlighting(array$U){echo
script_src(preg_replace("~\\?.*~","",ME)."?file=jush.js&version=6.1.1+5f8c6c15",true);$sg=preg_replace('~<(?=/script)~i','<\\',Driver::jushModule());echo($sg?script("addEventListener('DOMContentLoaded', () => {\n$sg\n});"):"");if(support("sql")){echo"<script".nonce().">\n";if($U){$Ff=array();foreach($U
as$S=>$V)$Ff[]=js_escape_re($S);echo"var jushLinks = { ".JUSH.":";json_row(js_escape(ME).(support("table")?"table":"select").'=$&','/\b(?<!\$)('.implode('|',$Ff).')(?!\$)\b/g',false);$Bj=array("sql","check","event","procedure","trigger","view","type","table","processlist");json_row('');echo"};\n";foreach(array("bac","bra","sqlite_quo","mssql_bra")as$X)echo"jushLinks.$X = jushLinks.".JUSH.";\n";if(array_intersect_key($_GET,array_flip(array("sql","check","event","procedure","trigger","view")))){$Fj=(isset($_GET["trigger"])?array('INSERT INTO','UPDATE','DELETE FROM'):(isset($_GET["check"])?array():(isset($_GET["view"])?array('SELECT'):null)));$Ha=Driver::jushAutocomplete($U,$Fj);echo($Ha?"addEventListener('DOMContentLoaded', () => { autocompleter = $Ha; });\n":"");}}echo"</script>\n";}echo
script("syntaxHighlighting('".doc_version()."', '".connection()->flavor."');");}function
databasesPrint($pg){if(support("single_db"))return;$g=adminer()->databases();if(DB&&$g&&!in_array(DB,$g))array_unshift($g,DB);echo"<form action=''>\n<p id='dbs'>\n";hidden_fields_get();$Yb=on('mousedown','dbMouseDown').on('change','dbChange');echo"<label title='".lang(35)."'>".lang(78).": ".($g?html_select("db",array(""=>"")+group_system($g),DB,$Yb):"<input name='db' value='".h(DB)."' autocapitalize='off' size='19'>\n")."</label>","<input type='submit' value='".lang(25)."'".($g?" class='hidden'":"").">\n";foreach(array("import","sql","schema","dump","privileges")as$X){if(isset($_GET[$X])){echo
input_hidden($X);break;}}echo"</p></form>\n";}function
menuActions(array$ga,$pg){return$ga;}function
tablesPrint(array$U){echo"<ul id='tables'".on('mouseover','menuOver').on('mouseout','menuOut').">";foreach($U
as$S=>$Gj){$S="$S";$C=adminer()->tableName($Gj);if($C!=""&&!$Gj["dependent"])echo'<li><a href="'.h(ME).'select='.url_escape($S).'"'.bold($_GET["select"]==$S||$_GET["edit"]==$S,"select hover")." title='".lang(41)."'>".lang(79)."</a> ",(support("table")||support("indexes")?'<a href="'.h(ME).'table='.url_escape($S).'"'.bold(in_array($S,array($_GET["table"],$_GET["create"],$_GET["indexes"],$_GET["foreign"],$_GET["trigger"],$_GET["check"],$_GET["view"])),(is_view($Gj)?"view":"structure"))." title='".lang(42)."'>$C</a>":"<span>$C</span>")."\n";}echo"</ul>\n";}function
showVariables(){return
show_variables();}function
showStatus(){return
show_status();}function
processList(){return
process_list();}function
killProcess($s){return
kill_process($s);}}class
Plugins{private
static$append=array('dumpFormat'=>true,'dumpOutput'=>true,'editRowPrint'=>true,'editFunctions'=>true,'config'=>true);var$plugins;var$drivers=array();var$driverFiles=array();var$error='';private$hooks=array();function
__construct($Uh){$Bc=SqlDriver::$drivers;$oe=" href='https://www.adminer.org/plugins/#use'".target_blank();if($Uh===null){$Uh=array();$Pa="adminer-plugins";if(is_dir($Pa)){foreach(glob("$Pa/*.php")as$m){$wd=SqlDriver::$drivers;$this->includeOnce($m);foreach(array_diff_key(SqlDriver::$drivers,$wd)as$s=>$C)$this->driverFiles[$s]=$m;}}if(file_exists("$Pa.php")){$Ge=$this->includeOnce("$Pa.php");if(is_array($Ge)){foreach($Ge
as$x=>$Rh)$Uh[is_object($Rh)?get_class($Rh):$x]=$Rh;}else$this->error
.=lang(80,"<b>$Pa.php</b>",$oe)."<br>";}foreach(get_declared_classes()as$mb){if(!$Uh[$mb]&&(preg_match('~^Adminer\w~i',$mb)||is_subclass_of($mb,'Adminer\Plugin'))){$yi=new
\ReflectionClass($mb);$Cb=$yi->getConstructor();if($Cb&&$Cb->getNumberOfRequiredParameters())$this->error
.=lang(81,$oe,"<b>$mb</b>","<b>$Pa.php</b>")."<br>";else$Uh[$mb]=new$mb;}}}$Ve=array_filter($Uh,function($Rh){return!is_object($Rh);});if($Ve){$this->error
.=lang(82,$oe)."<br>";$Uh=array_diff_key($Uh,$Ve);}$this->drivers=array_diff_key(SqlDriver::$drivers,$Bc);$this->plugins=$Uh;$ja=new
Adminer;$Uh[]=$ja;$yi=new
\ReflectionObject($ja);foreach($yi->getMethods()as$mg){foreach($Uh
as$Rh){$C=$mg->getName();if(method_exists($Rh,$C))$this->hooks[$C][]=$Rh;}}}function
includeOnce($m){return
include_once"./$m";}static
function
checksum($m){$vd=str_replace("\r","",file_get_contents($m));$vd=preg_replace('~\n\tprotected \$translations = array\(.*?\n\t\);~s','',$vd);return
dechex(crc32($vd));}function
checksums(){$xd=array_values($this->driverFiles);foreach($this->plugins
as$Rh){$yi=new
\ReflectionObject($Rh);$xd[]=$yi->getFileName();}$K=array();foreach($xd
as$m)$K[basename($m,'.php')]=self::checksum($m);return$K;}static
function
officialChecksums(){return
array('adminer.js'=>'a0599090','backward-keys'=>'e65981f5','before-unload'=>'2a613523','config'=>'722eb4af','dark-switcher'=>'3d490dea','database-hide'=>'e304a899','designs'=>'ed7e44e3','dump-alter'=>'896b579e','dump-bz2'=>'f0d0e336','dump-date'=>'adc7f1c7','dump-json'=>'767dd321','dump-xml'=>'4fc3cd60','dump-zip'=>'93817d96','edit-foreign'=>'72ad1562','edit-textarea'=>'a24c3cc','editor-setup'=>'a7dc3a37','editor-views'=>'5c12b185','enum-option'=>'1e24970e','file-upload'=>'10add0e8','foreign-system'=>'ebb4c654','frames'=>'b0e1d11a','highlight-codemirror'=>'c5716555','highlight-monaco'=>'edd1b0af','highlight-prism'=>'267948e5','import-csv'=>'d429c77','login-ip'=>'4d174fea','login-otp'=>'5b5a68af','login-passkey'=>'f69f2f06','login-password-less'=>'e150daac','login-reverse-proxy'=>'24558ea2','login-servers'=>'19c42e45','login-ssl'=>'6ed147bc','login-table'=>'811f8cef','menu-links'=>'c78461b3','name-patterns'=>'84c10d09','remote-color'=>'ddeecc48','row-numbers'=>'eec8698c','select-email'=>'f84fbd2c','select-foreign'=>'fe3e58c8','select-image'=>'f55c0231','slugify'=>'dec64713','sql-gemini'=>'c60ab309','sql-log'=>'8e435000','table-indexes-structure'=>'a90cc0c9','table-structure'=>'a8458e02','tables-filter'=>'ec2bcd6e','timeout'=>'97321caf','version-github'=>'627cadf9','version-noverify'=>'966937e9','clickhouse'=>'92ca960d','elastic'=>'1582a04d','firebird'=>'1cccfc19','igdb'=>'4063cc0b','imap'=>'3da1022b','mongo'=>'63486492','redis'=>'79824392','simpledb'=>'b8e2cc7d',);}function
__call($C,array$zh){$za=array();foreach($zh
as$x=>$X)$za[]=&$zh[$x];$K=null;foreach($this->hooks[$C]as$Rh){$Y=call_user_func_array(array($Rh,$C),$za);if($Y!==null){if(!self::$append[$C])return$Y;$K=$Y+(array)$K;}}return$K;}}abstract
class
Plugin{protected$translations=array();function
description(){return$this->lang('');}function
screenshot(){return"";}protected
function
lang($t,$D=null){$za=func_get_args();$za[0]=idx($this->translations[LANG],$t)?:$t;return
call_user_func_array('Adminer\lang_format',$za);}}class
Password{private$password_hash;private$password_matches=null;function
__construct($Ih){$this->password_hash=$Ih;}function
description(){return
lang(83);}function
credentials(){$G=get_password();return
array(SERVER,$_GET["username"],($this->passwordMatches($G)&&!password_required()?"":$G));}function
login($Jf,$G){if($this->passwordMatches($G))return
true;}protected
function
passwordMatches($G){if($this->password_matches===null)$this->password_matches=(function_exists('password_verify')&&password_verify(strval($G),$this->password_hash));return$this->password_matches;}}Adminer::$instance=(function_exists('adminer_object')?adminer_object():(is_dir("adminer-plugins")||file_exists("adminer-plugins.php")?new
Plugins(null):new
Adminer));define('Adminer\JUSH',Driver::$jush);define('Adminer\SERVER',"".$_GET[DRIVER]);define('Adminer\DB',"$_GET[db]");define('Adminer\ME',preg_replace('~\?.*~','',relative_uri()).'?'.(sid()?SID.'&':'').($_GET["ext"]?"ext=".url_escape($_GET["ext"]).'&':'').(isset($_GET[DRIVER])?DRIVER."=".url_escape(SERVER).'&':'').(isset($_GET["username"])?"username=".url_escape($_GET["username"]).'&':'').(isset($_GET["db"])?'db='.url_escape(DB).'&'.(isset($_GET["ns"])?"ns=".url_escape($_GET["ns"])."&":""):''));if(isset($_GET["manifest"])){header("Content-Type: application/manifest+json; charset=utf-8");header("Cache-Control: no-cache");echo
json_encode(adminer()->manifest(),64|256);exit;}function
page_header($vk,$j="",$Va=array(),$wk="",$Jg=false,$yc=""){if($Jg){header("HTTP/1.1 404 Not Found");$j=($j?:lang(84));}page_headers();if(is_ajax()&&$j){page_messages($j);exit;}if(!ob_get_level())ob_start('ob_gzhandler',4096);$xk=$vk.($wk!=""?": $wk":"");$yk=strip_tags($xk.(SERVER!=""&&SERVER!="localhost"?h(" - ".SERVER):"")." - ".adminer()->name());echo'<!DOCTYPE html>
<html lang=\'',LANG,'\' dir=\'',lang(85),'\' class=\'',lang(85),' nojs\'>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="robots" content="noindex">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>',$yk,'</title>
<link rel="stylesheet" href="',h(preg_replace("~\\?.*~","",ME)."?file=default.css&version=6.1.1+5f8c6c15"),'">
';$Pb=adminer()->css();if(is_int(key($Pb)))$Pb=array_fill_keys($Pb,'light');$ge=in_array('light',$Pb)||in_array('',$Pb);$ee=in_array('dark',$Pb)||in_array('',$Pb);$Tb=($ge?($ee?null:false):($ee?:null));$bg=" media='(prefers-color-scheme: dark)'";if($Tb!==false)echo"<link rel='stylesheet'".($Tb?"":$bg)." href='".h(preg_replace("~\\?.*~","",ME)."?file=dark.css&version=6.1.1+5f8c6c15")."'>\n";echo"<meta name='color-scheme' content='".($Tb===null?"light dark":($Tb?"dark":"light"))."'>\n",script_src(preg_replace("~\\?.*~","",ME)."?file=functions.js&version=6.1.1+5f8c6c15");if(adminer()->head($Tb))echo"<link rel='icon' href='data:image/gif;base64,"."R0lGODlhEAAQAJEAAAQCBPz+/PwCBAROZCH5BAEAAAAALAAAAAAQABAAAAI2hI+pGO1rmghihiUdvUBnZ3XBQA7f05mOak1RWXrNq5nQWHMKvuoJ37BhVEEfYxQzHjWQ5qIAADs='>\n","<link rel='apple-touch-icon' href='".h(preg_replace("~\\?.*~","",ME)."?file=logo.svg&version=6.1.1+5f8c6c15")."'>\n";if(adminer()->manifest())echo"<link rel='manifest' href='".h(preg_replace('~\?.*~','',ME)."?manifest=")."' crossorigin='use-credentials'>\n";foreach($Pb
as$hl=>$qg){$b=($qg=='dark'&&!$Tb?$bg:($qg=='light'&&$ee?" media='(prefers-color-scheme: light)'":""));echo"<link rel='stylesheet'$b href='".h($hl)."'>\n";}echo"\n<body class='";adminer()->bodyClass();echo"'>\n",script((isset($_COOKIE["adminer_version"])||!adminer()->verifyVersion()?"":"onload = partial(verifyVersion, '".VERSION."');\n")."
const offlineMessage = '".js_escape(lang(86))."';
const numberFormat = '".js_escape(lang(6))."';
const numberDigits = '".js_escape(lang(7))."';
const urlSeparators = '".js_escape(ini_get("arg_separator.input"))."';"),"<div id='help' class='jush-".JUSH." jsonly hidden'".on('mouseover','helpKeep').on('mouseout','helpMouseout')."></div>\n","<div id='content'>\n","<span id='menuopen' class='jsonly'".on('click','menuToggle')."><button title='".lang(87)."' class='icon icon-move' aria-expanded='false'></button></span>\n";if($Va!==null){$_=substr(preg_replace('~\b(username|db|ns)=[^&]*&~','',ME),0,-1);echo'<p id="breadcrumb"><a href="'.h($_?:".").'">'.get_driver(DRIVER).'</a> » ';$_=substr(preg_replace('~\b(db|ns)=[^&]*&~','',ME),0,-1);$P=adminer()->serverName(SERVER);$P=($P!=""?$P:lang(88));if($Va===false)echo"$P\n";else{echo"<a href='".h($_.(DB!=""&&support("single_db")?"&db=":""))."' accesskey='1' title='Alt+Shift+1'>$P</a> » ";$Wi="";if(is_string($Va)){$Wi=$Va;$Va=array();}if($_GET["ns"]!=""||(DB!=""&&is_array($Va))){$Zb="$_&db=".url_escape(DB).(support("scheme")?"&ns=":"").(support("single_table")?"&select=":"");echo'<a href="'.h($Zb.($_GET["ns"]==""?$Wi:"")).'">'.h(DB).'</a> » ';}if(is_array($Va)){if($_GET["ns"]!="")echo'<a href="'.h(substr(ME,0,-1).$Wi).'">'.h($_GET["ns"]).'</a> » ';foreach($Va
as$x=>$X){$jc=(is_array($X)?$X[1]:h($X));if($jc!="")echo"<a href='".h(ME."$x=").url_escape(is_array($X)?$X[0]:$X)."'>$jc</a> » ";}}echo"$vk\n";}}echo"<h2>$xk$yc</h2>\n","<div id='ajaxstatus' role='status' class='jsonly'></div>\n";restart_session();page_messages($j);adminer()->serviceWorker();$g=&get_session("dbs");if(DB!=""&&$g&&!in_array(DB,$g,true))$g=null;stop_session();define('Adminer\PAGE_HEADER',1);ob_flush();flush();if($Jg){page_footer($Jg===true?"":$Jg);exit;}}function
service_worker(){$Ai=has_passwords();$nb=($Ai?"navigator.serviceWorker.register('".js_escape(preg_replace('~\?.*~','',ME)."?file=worker.js&version=6.1.1+5f8c6c15")."', {scope: location.pathname}).catch(() => {});":"navigator.serviceWorker.getRegistration().then(registration => registration && registration.unregister());
	caches.keys().then(keys => keys.forEach(key => key.startsWith('adminer-') && caches.delete(key)));");echo
script("if (navigator.serviceWorker) {\n\t$nb\n}");}function
has_passwords(){foreach((array)$_SESSION["pwds"]as$kj){foreach($kj
as$pl){foreach($pl
as$G){if($G!==null)return
true;}}}return
false;}function
page_headers(){header("Content-Type: text/html; charset=utf-8");header("Cache-Control: no-cache");header("X-Frame-Options: deny");header("X-XSS-Protection: 0");header("X-Content-Type-Options: nosniff");header("Referrer-Policy: origin-when-cross-origin");foreach(adminer()->csp(csp())as$Ob){$ke=array();foreach($Ob
as$x=>$X)$ke[]="$x $X";header("Content-Security-Policy: ".implode("; ",$ke));}adminer()->headers();}function
csp(){return
array(array("script-src"=>"'self' 'unsafe-inline' 'nonce-".get_nonce()."' 'strict-dynamic'","connect-src"=>"'self' https://www.adminer.org","frame-src"=>"https://www.adminer.org","object-src"=>"'none'","base-uri"=>"'none'","form-action"=>"'self'",),);}function
design_checksums(){$ml=array();foreach(array_keys(adminer()->css())as$hl)$ml[preg_replace('~\?.*~','',$hl)]=true;$K=array();foreach(array("adminer.css","adminer-dark.css")as$m){if($ml[$m]&&file_exists($m)){preg_match('~^/\* Adminer design ([-\w]+) \*/~',file_get_contents($m),$B);$K[$m]=array((string)$B[1],Plugins::checksum($m));}}return$K;}function
official_design_checksums(){return
array('adminer-border/adminer.css'=>'ec757f3e','adminer-dark/adminer-dark.css'=>'a26bcd7b','brade/adminer.css'=>'be4161f0','bueltge/adminer.css'=>'1a8f00b4','cpanel/adminer.css'=>'59ce604e','dracula/adminer-dark.css'=>'cfaf61dd','esterka/adminer.css'=>'1f805f36','flat/adminer.css'=>'49a61af9','galkaev/adminer-dark.css'=>'16c46f94','haeckel/adminer.css'=>'147a3565','hever/adminer.css'=>'ef0e1948','konya/adminer.css'=>'2b409696','lavender-light/adminer.css'=>'bf03f5d7','lucas-sandery/adminer.css'=>'6596353','mancave/adminer-dark.css'=>'e1ac813d','mvt/adminer.css'=>'ebd3afdc','nette/adminer.css'=>'5ab360e7','ng9/adminer.css'=>'488583cf','nicu/adminer.css'=>'216f097b','pappu687/adminer.css'=>'b58d128c','paranoiq/adminer.css'=>'64d27e5','pepa-linha/adminer.css'=>'baf25f0','pokorny/adminer.css'=>'ee9eea6d','price/adminer.css'=>'81be9a85','rmsoft/adminer.css'=>'6cd4a237','rmsoft_blue-dark/adminer.css'=>'32102a8','rmsoft_blue/adminer.css'=>'7d8d5b18','win98/adminer.css'=>'e82d63c3',);}function
version_iframe(){return(isset($_COOKIE["adminer_version"])||!adminer()->verifyVersion()?"":"<noscript><iframe sandbox src='https://www.adminer.org/version/?current=".VERSION."&amp;noscript=1'></iframe></noscript>");}function
get_nonce(){static$Ig;if(!$Ig)$Ig=base64_encode(rand_string());return$Ig;}function
page_messages($j){$gl=preg_replace('~^[^?]*~','',$_SERVER["REQUEST_URI"]);$ig=idx($_SESSION["messages"],$gl);if($ig){echo"<div class='message'>".implode("</div>\n<div class='message'>",$ig)."</div>".script("messagesPrint();");unset($_SESSION["messages"][$gl]);}if($j)echo"<div class='error'>$j</div>\n";if(adminer()->error)echo"<div class='error'>".adminer()->error."</div>\n";}function
page_footer($pg=""){echo"</div>\n\n<div id='foot' class='foot'>\n<div id='menu'>\n";adminer()->navigation($pg);echo"</div>\n";if($pg!="auth")echo'<form action="" method="post">
<p class="logout">
<span title="',lang(33),'">',h($_GET["username"])."\n",'</span>
<input type=\'submit\' name=\'logout\' value=\'',lang(89),'\' id=\'logout\'>
',input_token(),'</form>
';echo"</div>\n\n",script("setupSubmitHighlight(document);");}function
int32($xg){while($xg>=2147483648)$xg-=4294967296;while($xg<=-2147483649)$xg+=4294967296;return(int)$xg;}function
long2str(array$W,$Al){$Pi='';foreach($W
as$X)$Pi
.=pack('V',$X);if($Al)return
substr($Pi,0,end($W));return$Pi;}function
str2long($Pi,$Al){$W=array_values(unpack('V*',str_pad($Pi,4*ceil(strlen($Pi)/4),"\0")));if($Al)$W[]=strlen($Pi);return$W;}function
xxtea_mx($Kl,$Jl,$Oj,$if){return
int32((($Kl>>5&0x7FFFFFF)^$Jl<<2)+(($Jl>>3&0x1FFFFFFF)^$Kl<<4))^int32(($Oj^$Jl)+($if^$Kl));}function
encrypt_string($Ij,$x){if($Ij=="")return"";$x=array_values(unpack("V*",pack("H*",md5($x))));$W=str2long($Ij,true);$xg=count($W)-1;$Kl=$W[$xg];$Jl=$W[0];$ni=floor(6+52/($xg+1));$Oj=0;while($ni-->0){$Oj=int32($Oj+0x9E3779B9);$Ic=$Oj>>2&3;for($uh=0;$uh<$xg;$uh++){$Jl=$W[$uh+1];$wg=xxtea_mx($Kl,$Jl,$Oj,$x[$uh&3^$Ic]);$Kl=int32($W[$uh]+$wg);$W[$uh]=$Kl;}$Jl=$W[0];$wg=xxtea_mx($Kl,$Jl,$Oj,$x[$uh&3^$Ic]);$Kl=int32($W[$xg]+$wg);$W[$xg]=$Kl;}return
long2str($W,false);}function
decrypt_string($Ij,$x){if($Ij=="")return"";if(!$x)return
false;$x=array_values(unpack("V*",pack("H*",md5($x))));$W=str2long($Ij,false);$xg=count($W)-1;$Kl=$W[$xg];$Jl=$W[0];$ni=floor(6+52/($xg+1));$Oj=int32($ni*0x9E3779B9);while($Oj){$Ic=$Oj>>2&3;for($uh=$xg;$uh>0;$uh--){$Kl=$W[$uh-1];$wg=xxtea_mx($Kl,$Jl,$Oj,$x[$uh&3^$Ic]);$Jl=int32($W[$uh]-$wg);$W[$uh]=$Jl;}$Kl=$W[$xg];$wg=xxtea_mx($Kl,$Jl,$Oj,$x[$uh&3^$Ic]);$Jl=int32($W[0]-$wg);$W[0]=$Jl;$Oj=int32($Oj-0x9E3779B9);}return
long2str($W,true);}$Ph=array();if($_COOKIE["adminer_permanent"]){foreach(explode(" ",$_COOKIE["adminer_permanent"])as$X){list($x)=explode(":",$X);$Ph[$x]=$X;}}function
add_invalid_login(){$Na=get_temp_dir()."/adminer-invalid";foreach(glob("$Na*")?:array($Na)as$m){$o=file_open_lock($m);if($o)break;}if(!$o)$o=file_open_lock("$Na-".rand_string());if(!$o)return;$Xe=json_decode(stream_get_contents($o),true);$sk=time();if($Xe){foreach($Xe
as$Ye=>$X){if($X[0]<$sk)unset($Xe[$Ye]);}}$Ve=&$Xe[adminer()->bruteForceKey()];if(!$Ve)$Ve=array($sk+30*60,0);$Ve[1]++;file_write_unlock($o,json_encode($Xe));}function
check_invalid_login(array&$Ph){$Xe=array();foreach(glob(get_temp_dir()."/adminer-invalid*")as$m){$o=file_open_lock($m);if($o){$Xe=json_decode(stream_get_contents($o),true);file_unlock($o);break;}}$x=adminer()->bruteForceKey();$Ve=idx($Xe,$x,array());$Hg=($Ve[1]>29?$Ve[0]-time():0);if($Hg>0){$j=lang(90,ceil($Hg/60));if($_SERVER["HTTP_X_FORWARDED_FOR"]!=""&&$x==$_SERVER["REMOTE_ADDR"])$j
.='<br>'.lang(91,'<b>login-reverse-proxy</b>'," href='https://www.adminer.org/plugins/?version=".VERSION."'".target_blank());auth_error($j,$Ph,false);}}function
password_required(){static$K;if($K===null){$K=(bool)get_session("password_required");if(!$K){$Nb=adminer()->credentials();$K=!is_object(Driver::connect($Nb[0],$Nb[1],""));if($K)set_session("password_required",true);}}return$K;}function
require_password_link($G){$tg="<a href='https://www.adminer.org/password/'".target_blank().">".lang(92)."</a>";if(!function_exists('password_hash'))return" $tg";$Sh=($G!==null?$G:base64_encode(substr(pack("H*",rand_string()),0,12)));$je=password_hash($Sh,PASSWORD_DEFAULT);$m="adminer-plugins.php";$gd=file_exists("adminer-plugins.php");if($gd)$Ue=($G!==null?lang(93,"<b>$m</b>"):lang(94,"<b>$m</b>","<b>$Sh</b>"));else{$m="<button name='password_less' value='".h($je)."' class='link'>$m</button>";$Ue=($G!==null?lang(95,$m):lang(96,$m,"<b>$Sh</b>"));}$Df="\t<a>new</a> Adminer\\Password(<span class='jush-apo'>'".h($je)."'</span>),";$K="<p>$Ue
<pre><code class='jush'>".($gd?$Df:"&lt;?php\n<a>return</a> <a>array</a>(\n$Df\n);")."</code></pre>
<p>$tg
";return" <a href='#password-less' class='toggle'>".lang(97)."</a>
<div id='password-less' class='hidden'>".($gd?$K:"<form action='' method='post'>\n".$K.input_token()."</form>")."</div>";}if(preg_match('~^[-\w$./]+$~',$_POST["password_less"])&&verify_token()){header("Content-Type: application/octet-stream");header("Content-Disposition: attachment; filename=adminer-plugins.php");echo"<?php\nreturn array(\n\tnew Adminer\\Password('$_POST[password_less]'),\n);\n";exit;}$Fa=$_POST["auth"];if($Fa&&(!adminer()->verifyLoginToken()||verify_token())){session_regenerate_id();$wl=$Fa["driver"];$P=$Fa["server"];$ol=$Fa["username"];$G=(string)$Fa["password"];$h=$Fa["db"];set_password($wl,$P,$ol,$G);$_SESSION["db"][$wl][$P][$ol][$h]=true;if($Fa["permanent"]){$x=implode("-",array_map('base64_encode',array($wl,$P,$ol,$h)));$ii=adminer()->permanentLogin(true);$Ph[$x]="$x:".base64_encode($ii?encrypt_string($G,$ii):"");cookie("adminer_permanent",implode(" ",$Ph));}if(!array_diff(array_keys($_POST),array("auth","token"))||$wl!=DRIVER||$P!=SERVER||$ol!==$_GET["username"]||$h!=DB)redirect(auth_url($wl,$P,$ol,$h));}elseif($_POST["logout"]&&(!$_SESSION["token"]||verify_token())){Driver::disconnect();foreach(array("pwds","db","dbs","queries")as$x)set_session($x,null);unset_permanent($Ph);redirect(substr(preg_replace('~\b(username|db|ns)=[^&]*&~','',ME),0,-1),lang(98).' '.lang(99));}elseif($Ph&&!$_SESSION["pwds"]){session_regenerate_id();$ii=adminer()->permanentLogin();foreach($Ph
as$x=>$X){list(,$lb)=explode(":",$X);list($wl,$P,$ol,$h)=array_map('base64_decode',explode("-",$x));set_password($wl,$P,$ol,decrypt_string(base64_decode($lb),$ii));$_SESSION["db"][$wl][$P][$ol][$h]=true;}}function
unset_permanent(array&$Ph){foreach($Ph
as$x=>$X){list($wl,$P,$ol,$h)=array_map('base64_decode',explode("-",$x));if($wl==DRIVER&&$P==SERVER&&$ol==$_GET["username"]&&$h==DB)unset($Ph[$x]);}cookie("adminer_permanent",implode(" ",$Ph));}function
auth_error($j,array&$Ph,$We=true){$lj=session_name();if(isset($_GET["username"])){header("HTTP/1.1 403 Forbidden");if(($_COOKIE[$lj]||$_GET[$lj])&&!$_SESSION["token"])$j=lang(100);elseif($We&&($G=get_password())!==null){restart_session();add_invalid_login();if($G===false)$j
.=($j?'<br>':'').lang(101,target_blank(),'<code>permanentLogin()</code>');set_password(DRIVER,SERVER,$_GET["username"],null);unset_permanent($Ph);}}if(!$_COOKIE[$lj]&&$_GET[$lj]&&ini_bool("session.use_only_cookies"))$j=lang(102);$zh=session_get_cookie_params();cookie("adminer_key",($_COOKIE["adminer_key"]?:rand_string()),$zh["lifetime"]);if(!$_SESSION["token"])$_SESSION["token"]=rand(1,1e6);page_header(lang(36),$j,null);echo"<form action='' method='post'>\n","<div>";if(hidden_fields($_POST,array("auth","token")))echo"<p class='message'>".lang(103)."\n";echo
input_token(),"</div>\n";adminer()->loginForm();echo"</form>\n";page_footer("auth");exit;}if(isset($_GET["username"])&&!class_exists('Adminer\Db')){unset($_SESSION["pwds"][DRIVER]);unset_permanent($Ph);page_header(lang(104),lang(105,implode(", ",Driver::$extensions)),false);page_footer("auth");exit;}$Ab='';if(isset($_GET["username"])&&is_string(get_password())){check_invalid_login($Ph);$Nb=adminer()->credentials();$Ab=Driver::connect($Nb[0],$Nb[1],$Nb[2]);if(is_object($Ab)){Db::$instance=$Ab;Driver::$instance=new
Driver($Ab);if($Ab->flavor)save_settings(array("vendor-".DRIVER."-".SERVER=>get_driver(DRIVER)));}}$Jf=null;if(!is_object($Ab)||($Jf=adminer()->login($_GET["username"],get_password()))!==true){$j=(is_string($Ab)?nl_br(h($Ab)):(is_string($Jf)?$Jf:lang(106))).(preg_match('~^ | $~',get_password())?'<br>'.lang(107):'');auth_error($j,$Ph);}if($_POST["logout"]&&$_SESSION["token"]&&!verify_token()){page_header(lang(89),lang(108));page_footer("db");exit;}if(!$_SESSION["token"])$_SESSION["token"]=rand(1,1e6);stop_session(true);if($Fa&&$_POST["token"])$_POST["token"]=get_token();$j='';if($_POST){if(!verify_token()){header("HTTP/1.1 403 Forbidden");$j=lang(108).' '.lang(109);}}elseif($_SERVER["REQUEST_METHOD"]=="POST"){header("HTTP/1.1 413 Content Too Large");$j=lang(110,"<b>post_max_size</b>");if(isset($_GET["sql"]))$j
.=' '.lang(111);}function
print_select_result($J,$f=null,array$jh=array(),&$z=0,&$Jc=false){$Ff=array();$v=array();$e=array();$U=array();$fi=array();$Lc=array();$Sk=array();$K=array();$rg=$Jc;$Jc=false;for($r=0;(!$z||$r<$z)&&($L=$J->fetch_row());$r++){if(!$r){echo"<div class='scrollable'>\n","<table class='nowrap odds'".($rg?on('click','tableClick').on('dblclick','tableClick').on('keydown','editingKeydown'):"").">\n","<thead><tr>";for($w=0;$w<count($L);$w++){$k=$J->fetch_field();$C=$k->name;$S=(isset($k->table)?$k->table:"");$ih=(isset($k->orgtable)?$k->orgtable:"");$hh=(isset($k->orgname)?$k->orgname:$C);$Rk=driver()->typeName($k);if($jh&&JUSH=="sql")$Ff[$w]=($C=="table"?"table=":($C=="possible_keys"?"indexes=":null));elseif($ih!=""){$pa=($S!=""?$S:$ih);if($S!="")$K[$S]=$ih;if(!isset($v[$pa])){if(!isset($fi[$ih])){$fi[$ih]=array();foreach(indexes($ih,$f)as$u){if($u["type"]=="PRIMARY"){$fi[$ih]=array_flip($u["columns"]);break;}}}$U[$pa]=$ih;$v[$pa]=$fi[$ih];$e[$pa]=$fi[$ih];}if(isset($e[$pa][$hh])){unset($e[$pa][$hh]);$v[$pa][$hh]=$w;$Ff[$w]=$pa;}elseif($rg&&isset($k->orgname)&&$k->db==DB&&!is_blob(array("type"=>$Rk)))$Lc[$w]=array($pa,$hh,preg_match('~text|json|lob~',$Rk));}$Sk[$w]=$Rk;echo"<th title='".h(trim(($ih!=""?"$ih.$hh":($k->name!=$hh?$hh:""))." ".$Rk))."'>".h($C).($jh?'':"");}foreach($Lc
as$w=>$Ya){if($e[$Ya[0]])unset($Lc[$w]);}echo"<tbody>\n";}$Ae=array();foreach($v
as$pa=>$u){if($u&&!$e[$pa]){$t="";foreach($u
as$c=>$w){if($L[$w]===null){$t=null;break;}$t
.="&where[".url_escape(bracket_escape($c))."]=".url_escape($L[$w]);}$Ae[$pa]=$t;}}echo"<tr>";foreach($L
as$x=>$X){$_="";if(isset($Ff[$x])){if($jh&&JUSH=="sql"){$S=$L[array_search("table=",$Ff)];$_=ME.$Ff[$x].url_escape($jh[$S]!=""?$jh[$S]:$S);}elseif(idx($Ae,$Ff[$x])!==null)$_=ME."edit=".url_escape($U[$Ff[$x]]).$Ae[$Ff[$x]];}$b="";$Ya=idx($Lc,$x);if($Ya&&idx($Ae,$Ya[0])!==null&&is_utf8($X)){$Jc=true;$b=" data-name='".h("val[".bracket_escape($U[$Ya[0]])."][".bracket_escape(substr($Ae[$Ya[0]],1))."][".bracket_escape($Ya[1])."]")."' data-text='".($Ya[2]?1:0)."'";}$X=select_value($X,$_,array('type'=>(preg_match('~binary~',$Sk[$x])?'blob':$Sk[$x])),null);echo"<td".(preg_match(number_type(),$Sk[$x])?" class='number'":"")."$b>$X";}}$z=$r;echo($r?"</table>\n</div>":"<p class='message'>".lang(16))."\n";return$K;}function
textarea($C,$Y,$M=10,$rb=80,$hf=JUSH){echo"<textarea name='".h($C)."' rows='$M' cols='$rb' class='sqlarea jush-".h($hf)."' spellcheck='false' wrap='off'>";if(is_array($Y)){foreach($Y
as$X)echo
h($X[0])."\n\n\n";}else
echo
h($Y);echo"</textarea>";}function
select_input($b,array$eh,$Y="",$Qh=""){if($eh&&$Y!=""&&!isset($eh[$Y]))$eh=array($Y=>$Y)+$eh;$ik=($eh?"select":"input");return"<$ik$b".($eh?"><option value=''>$Qh".optionlist($eh,$Y,true)."</select>":" size='10' value='".h($Y)."' placeholder='$Qh'>");}function
json_row($x,$X=null,$ad=true){static$Ad=true;if($Ad)echo"{";if($x!=""){echo($Ad?"":",")."\n\t\"".addcslashes($x,"\r\n\t\"\\/").'": '.($X!==null?($ad?'"'.addcslashes($X,"\r\n\"\\/").'"':$X):'null');$Ad=false;}else{echo"\n}\n";$Ad=true;}}function
flat_collations(){$qb=collations();return(is_array(reset($qb))?call_user_func_array('array_merge',array_values($qb)):$qb);}function
edit_type($x,array$k,array$qb,array$Hd=array(),array$nd=array()){$V=(string)$k["type"];echo"<td><select name='".h($x)."[type]' class='type' aria-labelledby='label-type'".on_help_value().">";if($V&&!array_key_exists($V,driver()->types())&&!isset($Hd[$V])&&!in_array($V,$nd))$nd[]=$V;$Jj=driver()->structuredTypes();if($Hd)$Jj[lang(112)]=$Hd;echo
optionlist(array_merge($nd,$Jj),$V),"</select><td>","<input name='".h($x)."[length]' value='".h($k["length"])."' size='3'".(!$k["length"]&&preg_match('~var(char|binary)$~',$V)?" class='required'":"")." aria-labelledby='label-length'>","<td class='options'>",($qb?"<input list='collations' name='".h($x)."[collation]'".option_types($V,'('.text_type().')$')." value='".h($k["collation"])."' placeholder='(".lang(113).")'>":''),(driver()->unsigned?"<select name='".h($x)."[unsigned]'".option_types($V,'^$|'.number_type()).'><option>'.optionlist(driver()->unsigned,$k["unsigned"]).'</select>':''),(isset($k['on_update'])?"<select name='".h($x)."[on_update]'".option_types($V,'timestamp|datetime').'>'.optionlist(array(""=>"(".lang(114).")","CURRENT_TIMESTAMP"),(preg_match('~^CURRENT_TIMESTAMP~i',$k["on_update"])?"CURRENT_TIMESTAMP":$k["on_update"])).'</select>':''),($Hd?"<select name='".h($x)."[on_delete]'".option_types($V,'`')."><option value=''>(".lang(115).")".optionlist(explode("|",driver()->onActions),$k["on_delete"])."</select> ":" ");}function
option_types($V,$Sk){return" data-types='".h($Sk)."'".(preg_match("~$Sk~",$V)?"":" class='hidden'");}function
process_length($y){if(JUSH=="mssql"&&preg_match('~^\s*\(?\s*max\s*\)?\s*$~i',$y))return"(max)";$Vc=driver()->enumLength;return(preg_match("~^\\s*\\(?\\s*$Vc(?:\\s*,\\s*$Vc)*+\\s*\\)?\\s*\$~",$y)&&preg_match_all("~$Vc~",$y,$Mf)?"(".implode(",",$Mf[0]).")":preg_replace('~^[0-9].*~','(\0)',preg_replace('~[^-0-9,+()[\]]~','',$y)));}function
process_in($X){$Vc=driver()->enumLength;if(preg_match("~^\\s*\\(?\\s*$Vc(?:\\s*,\\s*$Vc)*+\\s*\\)?\\s*\$~",$X)&&preg_match_all("~$Vc~",$X,$Mf))return"(".implode(", ",$Mf[0]).")";$K=array();foreach(explode(",",$X)as$ff)$K[]=q(trim($ff));return"(".implode(", ",$K).")";}function
process_type(array$k,$ob="COLLATE"){return" ".(is_user_type($k["type"])?idf_escape($k["type"]):$k["type"]).process_length($k["length"]).(preg_match(number_type(),$k["type"])&&in_array($k["unsigned"],driver()->unsigned)?" $k[unsigned]":"").(preg_match('~'.text_type().'~',$k["type"])&&$k["collation"]?" $ob ".(JUSH=="mssql"?$k["collation"]:q($k["collation"])):"");}function
process_field(array$k,array$Pk){if($k["on_update"])$k["on_update"]=str_ireplace("current_timestamp()","CURRENT_TIMESTAMP",$k["on_update"]);return
array(idf_escape(trim($k["field"])),process_type($Pk),($k["null"]?" NULL":" NOT NULL"),default_value($k),(preg_match('~timestamp|datetime~',$k["type"])&&$k["on_update"]?" ON UPDATE $k[on_update]":""),(support("comment")&&$k["comment"]!=""?" COMMENT ".q($k["comment"]):""),($k["auto_increment"]?auto_increment():null),);}function
default_value(array$k){if($k["default"]===null)return"";$i=str_replace("\r","",$k["default"]);$Td=$k["generated"];$R=!preg_match('~]$~',$k["length"])&&(preg_match('~char|binary|text|json|enum|set|String~',$k["type"])||driver()->enumLength($k));return(in_array($Td,driver()->generated)?(JUSH=="mssql"?" AS ($i)".($Td=="VIRTUAL"?"":" $Td"):" GENERATED ALWAYS AS ($i) $Td"):(preg_match('~^GENERATED ~i',$i)?" $i":" DEFAULT ".($R||preg_match('~^(?![a-z])~i',$i)?(JUSH=="sql"&&preg_match('~text|json~',$k["type"])?"(".q($i).")":q($i)):str_ireplace("current_timestamp()","CURRENT_TIMESTAMP",(JUSH=="sqlite"?"($i)":$i)))));}function
edit_fields(array$l,array$qb,$V="TABLE",array$Hd=array()){$l=array_values($l);$ec=(($_POST?$_POST["defaults"]:get_setting("defaults"))?"":" class='hidden'");$vb=(($_POST?$_POST["comments"]:get_setting("comments"))?"":" class='hidden'");echo"<thead><tr>\n",($V=="PROCEDURE"?"<td>":""),"<th id='label-name'>".($V=="TABLE"?lang(116):lang(117)),"<th id='label-type'>".lang(49)."<textarea id='enum-edit' rows='4' cols='12' wrap='off' hidden></textarea>".script("qs('#enum-edit').onblur = editingLengthBlur;"),"<th id='label-length'>".lang(118),"<th>".lang(119);if($V=="TABLE")echo"<th id='label-null'>NULL\n","<th><input type='radio' name='auto_increment_col' value=''><abbr id='label-ai' title='".lang(51)."'>AI</abbr>",doc_link(array('sqlite'=>"autoinc.html",)),"<th id='label-default'$ec>".lang(52),(support("comment")?"<th id='label-comment'$vb>".lang(50):"");$tf=!support("move_col");echo"<td>".icon("plus","add[".($tf?count($l):0)."]","+",lang(120),($tf?on('click','editingAddLastRow'):"")),"<tbody".on('click','editingClick').on('input','editingInput').on('keydown','editingKeydown').">\n";foreach($l
as$r=>$k){$r++;$kh=$k[($_POST?"orig":"field")];$qc=(isset($_POST["add"][$r-1])||(isset($k["field"])&&!idx($_POST["drop_col"],$r)))&&(support("drop_col")||$kh=="");echo"<tr".($qc?"":" hidden").">\n",($V=="PROCEDURE"?"<td>".html_select("fields[$r][inout]",explode("|",driver()->inout),$k["inout"]):"")."<th>",(support("move_col")?icon("move","","↕",lang(121))." ":"");if($qc)echo"<input name='fields[$r][field]' value='".h($k["field"])."' data-maxlength='64' autocapitalize='off' aria-labelledby='label-name'".(isset($_POST["add"][$r-1])?" autofocus":"").">";echo
input_hidden("fields[$r][orig]",$kh);edit_type("fields[$r]",$k,$qb,$Hd);if($V=="TABLE"){echo"<td><label class='block'>".checkbox("fields[$r][null]",1,$k["null"],"","","","label-null")."</label>","<td><label class='block'><input type='radio' name='auto_increment_col' value='$r'".($k["auto_increment"]?" checked":"")." aria-labelledby='label-ai'></label>","<td$ec>".(driver()->generated?html_select("fields[$r][generated]",array_merge(array("","DEFAULT"),driver()->generated),$k["generated"])." ":checkbox("fields[$r][generated]",1,$k["generated"],"","","","label-default"));$b=" name='fields[$r][default]' aria-labelledby='label-default'";$Y=h($k["default"]);echo(preg_match('~\n~',$k["default"])?"<textarea$b rows='2' cols='30' style='vertical-align: bottom;'>\n$Y</textarea>":"<input$b value='$Y'>");if(support("comment")){$b=" name='fields[$r][comment]' data-maxlength='".(min_version(5.5)?1024:255)."' aria-labelledby='label-comment'";echo"<td$vb>".adminer()->commentInput('COLUMN',$b,$k["comment"]);}}echo"<td>",(support("move_col")?icon("plus","add[$r]","+",lang(120))." ":""),($kh==""||support("drop_col")?icon("cross","drop_col[$r]","x",lang(122)):"");}}function
process_fields(array&$l){if($_POST["add"]){$l=array_values($l);array_splice($l,key($_POST["add"]),0,array(array()));}return$_POST["add"]||$_POST["drop_col"];}function
drop_create($Cc,$Lb,$Ec,$ok,$Gc,$A,$hg,$fg,$gg,$Ug,$Eg){if($_POST["drop"])query_redirect($Cc,$A,$hg);elseif($Ug=="")query_redirect($Lb,$A,$gg);elseif(support("transaction_ddl")){driver()->begin();queries_redirect($A,$fg,queries($Cc)&&queries($Lb)&&driver()->commit());driver()->rollback();}elseif($Ug!=$Eg){$Mb=queries($Lb);queries_redirect($A,$fg,$Mb&&queries($Cc));if($Mb&&$Ec)queries($Ec);}else
queries_redirect($A,$fg,queries($ok)&&queries($Gc)&&queries($Cc)&&queries($Lb));}function
create_trigger($Vg,array$L){$uk=" $L[Timing] $L[Event]".(preg_match('~ OF~',$L["Event"])?" $L[Of]":"");return"CREATE TRIGGER ".idf_escape($L["Trigger"]).(JUSH=="mssql"?$Vg.$uk:$uk.$Vg).preg_replace('~[\s;]+$~',''," $L[Type]\n$L[Statement]").";";}function
q_dollar($R){$ic='$$';while(strpos($R.$ic,$ic)!=strlen($R))$ic='$_'.substr($ic,1);return$ic.$R.$ic;}function
routine_collate($pb){static$db=array();if($pb&&!$db){foreach(collations()as$cb=>$tl){foreach((array)$tl
as$X)$db[$X]=$cb;}}return($db[$pb]?"CHARACTER SET ".q($db[$pb])." ":"")."COLLATE";}function
create_routine($Li,array$L){$Q=array();$l=$L["fields"];ksort($l);foreach($l
as$k){if($k["field"]!=""){$Pe=(preg_match("~^(".driver()->inout.")\$~",$k["inout"])?$k["inout"]:"");$Q[]="\n  ".(JUSH=="mssql"?"@$k[field]".process_type($k).($Pe?" $Pe":""):($Pe?"$Pe ":"").idf_escape($k["field"]).process_type($k,routine_collate($k["collation"])));}}$gc="";$eh=array();foreach(routine_options($Li)as$x=>$ul){$Y=idx($L["options"],$x,"");if($x=="DEFINER")$gc=($Y?" $x=".implode("@",array_map('Adminer\q',explode("@",$Y,2))):"");elseif(!$ul){if($Y!="")$eh[]="$x ".q($Y);}elseif($Y!=reset($ul)&&in_array($Y,$ul))$eh[]=$Y;}$rf=$L["language"];$hc=preg_replace('~[\s;]+$~','',$L["definition"]);$zc=(JUSH=="pgsql"||($rf&&$rf!="sql"));$yh=($Q?implode(",",$Q)."\n":"");return"CREATE$gc $Li ".table(trim($L["name"])).(JUSH=="mssql"&&$Li=="PROCEDURE"?rtrim($yh):" ($yh)").($Li=="FUNCTION"?"\nRETURNS".process_type($L["returns"],routine_collate($L["returns"]["collation"])):"").($rf?" LANGUAGE $rf":"").($eh?"\n".implode(" ",$eh):"").($zc?" AS ".q_dollar("\n".trim($hc)."\n"):(JUSH=="mssql"?"\nAS":"")."\n$hc;");}function
remove_definer($I){$gc=implode("@",array_map('Adminer\idf_escape',explode("@",logged_user(),2)));return
preg_replace('(^([A-Z =]+) DEFINER='.preg_quote($gc).')','\1',$I);}function
object_name($V,$S,array$e){return
str_replace(array("{table}","{columns}"),array($S,implode("_",$e)),adminer()->namePattern($V));}function
format_foreign_key(array$n,$C=""){$h=$n["db"];$Kg=$n["ns"];return($C!=""?" CONSTRAINT ".idf_escape($C):"")." FOREIGN KEY (".implode(", ",array_map('Adminer\idf_escape',$n["source"])).") REFERENCES ".($h!=""&&$h!=$_GET["db"]?idf_escape($h).".":"").($Kg!=""&&$Kg!=$_GET["ns"]?idf_escape($Kg).".":"").idf_escape($n["table"])." (".implode(", ",array_map('Adminer\idf_escape',$n["target"])).")".(preg_match("~^(".driver()->onActions.")\$~",$n["on_delete"])?" ON DELETE $n[on_delete]":"").(preg_match("~^(".driver()->onActions.")\$~",$n["on_update"])?" ON UPDATE $n[on_update]":"").($n["deferrable"]?" $n[deferrable]":"");}function
tar_file($m,$zk){$K=pack("a100a8a8a8a12a12",$m,644,0,0,decoct($zk->size),decoct(time()));$ib=8*32;for($r=0;$r<strlen($K);$r++)$ib+=ord($K[$r]);$K
.=sprintf("%06o",$ib)."\0 ";echo$K,str_repeat("\0",512-strlen($K));$zk->send();echo
str_repeat("\0",511-($zk->size+511)%512);}function
doc_version(){$jj=connection()->server_info;if(JUSH=='oracle'){preg_match('~(?:.* |^)(\d+)\.\d+\.\d+\.\d+\.\d+~s',$jj,$B);return($B[1]>=18?$B[1]:"19");}$_i=(JUSH=='sql'||connection()->flavor=='cockroach'?'~^\d+\.\d+~':'~^\d\.?\d~');$xl=(preg_match($_i,$jj,$B)?$B[0]:"");if(JUSH=='mssql')return($xl>=15?"sql-server-ver$xl":($xl==12?"azuresqldb-current":"sql-server-2017"));return$xl;}function
doc_link(array$Lh,$pk="📖"){$xl=doc_version();$il=array('sql'=>"https://dev.mysql.com/doc/refman/$xl/en/",'sqlite'=>"https://www.sqlite.org/",'pgsql'=>"https://www.postgresql.org/docs/".(connection()->flavor=='cockroach'?"current":$xl)."/",'mssql'=>"https://learn.microsoft.com/en-us/sql/",'oracle'=>"https://docs.oracle.com/en/database/oracle/oracle-database/$xl/",);if(connection()->flavor=='maria'){$il['sql']="https://mariadb.com/kb/en/";$Lh['sql']=($Lh['mariadb']?:str_replace(".html","/",$Lh['sql']));}if(connection()->flavor=='cockroach'&&$Lh['cockroach']){$il['pgsql']="https://docs.cockroachlabs.com/docs/v$xl/";$Lh['pgsql']=$Lh['cockroach'];}return($Lh[JUSH]?" <a href='".h($il[JUSH].$Lh[JUSH].(JUSH=='mssql'?"?view=$xl":""))."'".target_blank()." class='doc' title='".lang(123)."'>$pk</a>":"");}function
db_size($h){if(!connection()->select_db($h))return"?";$K=0;foreach(table_status()as$T)$K+=$T["Data_length"]+$T["Index_length"];return
format_number($K);}function
set_utf8mb4($Lb){static$Q=false;if(!$Q&&preg_match('~\butf8mb4~i',$Lb)){$Q=true;echo"SET NAMES ".charset(connection()).";\n\n";}}if(DB==""&&isset($_GET["ns"]))redirect(remove_from_uri('ns'));if(!(DB!=""?connection()->select_db(DB):isset($_GET["sql"])||isset($_GET["dump"])||isset($_GET["database"])||isset($_GET["processlist"])||isset($_GET["privileges"])||isset($_GET["user"])||isset($_GET["variables"])||$_GET["script"]=="connect"||$_GET["script"]=="kill")){if(DB!=""||$_GET["refresh"]){restart_session();set_session("dbs",null);}if(DB!="")page_header(lang(35).": ".h(DB),adminer()->error(),true,"","db");else{if(!isset($_GET["db"])&&support("single_db")){$g=adminer()->databases();if($g)redirect(ME."db=".url_escape($g[0]));}if($_POST["db"]&&!$j)queries_redirect(substr(ME,0,-1),lang(124),drop_databases($_POST["db"]));page_header(lang(125),$j,false);echo"<p class='links'>\n";foreach(array('database'=>lang(126),'privileges'=>lang(71),'processlist'=>lang(127),'variables'=>lang(128),'status'=>lang(129),)as$x=>$X){if(support($x))echo"<a href='".h(ME)."$x='>$X</a>\n";}echo"<p>".lang(130,get_driver(DRIVER),"<b>".h(connection()->server_info)."</b>","<b>".connection()->extension."</b>")."\n","<p>".lang(131,"<b>".h(logged_user())."</b>")."\n";$g=adminer()->databases();if($g){$Si=support("scheme");$qb=collations();echo"<form action='' method='post'>\n","<table class='checkable odds'".on('click','tableClick').on('dblclick','tableClick').">\n","<thead><tr>".(support("database")?"<td class='hover'>":"")."<th".(JUSH!='mssql'?" aria-sort='ascending'":"").">".lang(35).(get_session("dbs")!==null?" - <a href='".h(ME)."refresh=1'>".lang(132)."</a>":"")."<th>".lang(133)."<th>".lang(134)."<th>".lang(135)." - <a href='".h(ME)."dbsize=1'".on('click','ajaxSetHtml',ME."script=connect").">".lang(136)."</a>"."<tbody>\n";$g=($_GET["dbsize"]?count_tables($g):array_flip($g));foreach($g
as$h=>$U){$Ki=h(preg_replace('~&db=[^&]*~','',ME))."db=".url_escape($h);$s=h("Db-".$h);echo"<tr>".(support("database")?"<td class='hover'>".checkbox("db[]",$h,in_array($h,(array)$_POST["db"]),"","","",$s):""),"<th><a href='$Ki' id='$s'>".h($h)."</a>";$pb=h(db_collation($h,$qb));echo"<td>".(support("database")?"<a href='$Ki".($Si?"&amp;ns=":"")."&amp;database=' title='".lang(67)."'>$pb</a>":$pb),"<td align='right'><a href='$Ki&amp;schema=' id='tables-".h($h)."' title='".lang(70)."'>".($_GET["dbsize"]?format_number($U):"?")."</a>","<td align='right' id='size-".h($h)."'>".($_GET["dbsize"]?db_size($h):"?"),"\n";}echo"</table>\n",(support("database")?"<div class='footer'><div>\n"."<fieldset><legend>".lang(137)." <span id='selected'></span></legend><div>\n"."<input type='hidden' name='all' value=''".on('click','countDbs').">\n"."<input type='submit' name='drop' value='".lang(138)."'".confirm().">\n"."</div></fieldset>\n"."</div></div>\n":""),input_token(),"</form>\n",script("tableCheck();");}$ja=adminer();$Uh=($ja
instanceof
Plugins?$ja->plugins:array());$Bc=($ja
instanceof
Plugins?$ja->drivers:array());$nc=design_checksums();if($Uh||$Bc||$nc){$jb=($ja
instanceof
Plugins?$ja->checksums():array());$Pg=Plugins::officialChecksums();$dl=function($hl){return" (<a href='$hl'".target_blank()." class='update'>".VERSION."</a>)";};$Th=function($vd)use($jb,$Pg,$dl){return($jb[$vd]&&$Pg[$vd]&&$jb[$vd]!==$Pg[$vd]?$dl("https://www.adminer.org/plugins/?version=".VERSION):"");};echo"<div class='plugins'>\n","<h3>".lang(139)."</h3>\n<ul>\n";foreach($Uh
as$Rh){$yi=new
\ReflectionObject($Rh);$kc=(method_exists($Rh,'description')?$Rh->description():"");if(!$kc){if(preg_match('~^/[\s*]+(.+)~',$yi->getDocComment(),$B))$kc=$B[1];}$Ti=(method_exists($Rh,'screenshot')?$Rh->screenshot():"");echo"<li><b>".get_class($Rh)."</b>".h($kc?": $kc":"").($Ti?" (<a href='".h($Ti)."'".target_blank().">".lang(140)."</a>)":"").$Th(basename((string)$yi->getFileName(),'.php'))."\n";}foreach($Bc
as$s=>$C)echo"<li><b>".h($s)."</b>: ".h($C).$Th(basename((string)$ja->driverFiles[$s],'.php'))."\n";if($nc){$Rg=official_design_checksums();foreach($nc
as$m=>$mc){list($C,$ib)=$mc;$Qg=$Rg["$C/$m"];echo"<li><b>".h($m)."</b>".h($C?": $C":"").($Qg&&$Qg!==$ib?$dl("https://www.adminer.org/?version=".VERSION."#extras"):"")."\n";}}echo"</ul>\n";adminer()->pluginsLinks();echo"</div>\n";}}page_footer("db");exit;}adminer()->afterConnect();class
TmpFile{private$handler;var$size=0;function
__construct(){$this->handler=tmpfile();}function
write($Eb){$this->size+=strlen($Eb);fwrite($this->handler,$Eb);}function
send(){fseek($this->handler,0);fpassthru($this->handler);fclose($this->handler);}}if($_GET["select"]!=""&&($_POST["edit"]||$_POST["clone"])&&!$_POST["save"])$_GET["edit"]=$_GET["select"];if(isset($_GET["download"])){$a=$_GET["download"];$l=fields($a);header("Content-Type: application/octet-stream");$ul=array_merge((array)$_GET["where"],(array)$_GET["val"]);header("Content-Disposition: attachment; filename=".friendly_url("$a-".implode("_",$ul)).".".friendly_url($_GET["field"]));$O=array(idf_escape($_GET["field"]));$J=driver()->select($a,$O,array(where($_GET,$l)),$O);$L=($J?$J->fetch_row():array());echo
driver()->value($L[0],$l[$_GET["field"]]);exit;}elseif(isset($_GET["table"])){$a=$_GET["table"];$l=fields($a);if(!$l)$j=adminer()->error();$T=table_status1($a);$C=adminer()->tableName($T);$j=$j?:h($T["Error"]);page_header(($l&&is_view($T)?$T['Engine']=='materialized view'?lang(141):lang(142):lang(143)).": ".($C!=""?$C:h($a)),$j,array(),"",!$l,($l?doc_link(array(JUSH=>driver()->tableHelp($a,is_view($T)))):""));$Ji=array();foreach($l
as$x=>$k)$Ji+=$k["privileges"];adminer()->selectLinks($T,(isset($Ji["insert"])||!support("table")?"":null));$ub=$T["Comment"];if($ub!="")echo"<p class='nowrap'>".lang(50).": ".adminer()->commentValue('TABLE',$ub)."\n";if($l)adminer()->tableStructurePrint($l,$T);function
tables_links(array$U){echo"<ul>\n";foreach($U
as$L){$_=preg_replace('~ns=[^&]*~',"ns=".url_escape($L["ns"]),ME);echo"<li><a href='".h($_."table=".url_escape($L["table"]))."'>".($L["ns"]!=$_GET["ns"]?"<b>".h($L["ns"])."</b>.":"").h($L["table"])."</a>";}echo"</ul>\n";}$Ne=driver()->inheritsFrom($a);if($Ne){echo"<h3>".lang(144)."</h3>\n";tables_links($Ne);}if(support("indexes")&&driver()->supportsIndex($T)){echo"<div>\n","<h3 id='indexes'>".lang(145)."</h3>\n";$v=indexes($a);if($v)adminer()->tableIndexesPrint($v,$T);if(driver()->supportsAlterIndex($T))echo'<p class="links hover"><a href="'.h(ME).'indexes='.url_escape($a).'">'.lang(146)."</a>\n";echo"</div>\n";}if(!is_view($T)&&driver()->supportsAlterTable($T)){if(fk_support($T)){echo"<div>\n","<h3 id='foreign-keys'>".lang(112)."</h3>\n";$Hd=foreign_keys($a);if($Hd){echo"<table>\n","<thead><tr><th>".lang(147)."<th>".lang(148)."<th>".lang(115)."<th>".lang(114)."<td class='hover'><tbody>\n";foreach($Hd
as$C=>$n){echo"<tr title='".h($C)."'>","<th><i>".implode("</i>, <i>",array_map('Adminer\h',$n["source"]))."</i>";$_=($n["db"]!=""?preg_replace('~db=[^&]*~',"db=".url_escape($n["db"]),ME):($n["ns"]!=""?preg_replace('~ns=[^&]*~',"ns=".url_escape($n["ns"]),ME):ME));echo"<td><a href='".h($_."table=".url_escape($n["table"]))."'>".($n["db"]!=""&&$n["db"]!=DB?"<b>".h($n["db"])."</b>.":"").($n["ns"]!=""&&$n["ns"]!=$_GET["ns"]?"<b>".h($n["ns"])."</b>.":"").h($n["table"])."</a>","(<i>".implode("</i>, <i>",array_map('Adminer\h',$n["target"]))."</i>)","<td>".h($n["on_delete"]),"<td>".h($n["on_update"]),'<td class="hover"><a href="'.h(ME.'foreign='.url_escape($a).'&name='.url_escape($C)).'">'.lang(149).'</a>',"\n";}echo"</table>\n";}echo'<p class="links hover"><a href="'.h(ME).'foreign='.url_escape($a).'">'.lang(150)."</a>\n","</div>\n";}if(support("check")){echo"<div>\n","<h3 id='checks'>".lang(151)."</h3>\n";$fb=driver()->checkConstraints($a);if($fb){echo"<table>\n";foreach($fb
as$x=>$X)echo"<tr title='".h($x)."'>","<td><code class='jush-".JUSH."'>".shorten_utf8(preg_replace('~\s+~',' ',ltrim($X)),80,"</code>"),"<td class='hover'><a href='".h(ME.'check='.url_escape($a).'&name='.url_escape($x))."'>".lang(149)."</a>","\n";echo"</table>\n";}echo'<p class="links hover"><a href="'.h(ME).'check='.url_escape($a).'">'.lang(152)."</a>\n","</div>\n";}}if(support(is_view($T)?"view_trigger":"trigger")&&driver()->supportsAlterTable($T)){echo"<div>\n","<h3 id='triggers'>".lang(153)."</h3>\n";$Mk=triggers($a);if($Mk){echo"<table>\n";foreach($Mk
as$x=>$X){echo"<tr valign='top'><td>".h($X[0])."<td>".h($X[1])."<th>".h($x)."<td class='hover'><a href='".h(ME.'trigger='.url_escape($a).'&name='.url_escape($x))."'>".lang(149)."</a>";$Li=$X[2];if($Li){$Mi=preg_replace('~ns=[^&]*~',"ns=".url_escape($Li["ns"]),ME).'function='.url_escape($Li["function"]).'&name='.url_escape($Li["name"]);echo", <a href='".h($Mi)."' title='".h($Li["name"])."'>".lang(154)."</a>";}echo"\n";}echo"</table>\n";}echo'<p class="links hover"><a href="'.h(ME).'trigger='.url_escape($a).'">'.lang(155)."</a>\n","</div>\n";}$oj=driver()->shadowTables($a);if($oj){echo"<h3 id='shadow-tables'>".lang(156)."</h3>\n";tables_links($oj);}$Me=driver()->inheritedTables($a);if($Me){echo"<h3 id='partitions'>".lang(157)."</h3>\n";$Bh=driver()->partitionsInfo($a);if($Bh)echo"<p><code class='jush-".JUSH."'>BY ".h("$Bh[partition_by]($Bh[partition])")."</code>\n";tables_links($Me);}}elseif(isset($_GET["schema"])){page_header(lang(70),"",array(),h(DB.($_GET["ns"]?".$_GET[ns]":"")));function
schema_column($S,array$xi,array&$e){if(!isset($e[$S])){$e[$S]=0;foreach((array)idx($xi,$S)as$C=>$zi){if($C!=$S)$e[$S]=max($e[$S],schema_column($C,$xi,$e)+1);}}return$e[$S];}function
type_class($V){foreach(array('char'=>'text','date'=>'time|year','binary'=>'blob','enum'=>'set',)as$x=>$X){if(preg_match("~$x|$X~",$V))return" class='$x'";}}$Zj=array();$bk=array();$ak=array();$sd=array();$ba=($_GET["schema"]?:$_COOKIE["adminer_schema-".str_replace(".","_",DB)]);preg_match_all('~([^:]+):([-0-9.]+)x([-0-9.]+)(_|$)~',$ba,$Mf,PREG_SET_ORDER);foreach($Mf
as$r=>$B){$Zj[$B[1]]=array((float)$B[2],(float)$B[3]);$bk[]="\n\t'".js_escape($B[1])."': [ $B[2], $B[3] ]";}$N=array();$xi=array();$Hd=array();$ra=driver()->allFields();$pe=array();$ck=array();foreach(table_status('',true)as$S=>$T){if(!is_view($T)){if(adminer()->tableName($T)!=""&&!$T["dependent"])$ck[$S]=$T;else$pe[$S]=true;}}foreach($ck
as$S=>$T){$H=0;$N[$S]["fields"]=array();foreach($ra[$S]as$k){$H+=1.25;$sd[$S][$k["field"]]=$H;$N[$S]["fields"][$k["field"]]=$k;}foreach(adminer()->foreignKeys($S)as$X){if($X["db"]==""&&$X["ns"]==""&&!$pe[$X["table"]]){$Hd[$S][]=$X;$xi[$X["table"]][$S]=array();}}}$e=array();$Vd=array();$Il=array();$ae=array();foreach(array_keys($N)as$C)schema_column($C,$xi,$e);arsort($e);foreach($e
as$C=>$d){$ng=null;foreach((array)idx($Hd,$C)as$X){if($X["table"]!=$C&&$N[$X["table"]])$ng=($ng===null?$e[$X["table"]]:min($ng,$e[$X["table"]]));}$e[$C]=max($d,(int)$ng-1);}foreach($N
as$C=>$S){$d=$e[$C];$Vd[$d][]=$C;$rk=.75*strlen($C);foreach($S["fields"]as$k)$rk=max($rk,.65*strlen($k["field"]));$Il[$d]=max(idx($Il,$d,0),ceil($rk)+1);}foreach($Hd
as$C=>$tl){foreach($tl
as$X){$Zd=$e[$C]+(idx($e,$X["table"],$e[$C])>$e[$C]?1:0);$ae[$Zd]=idx($ae,$Zd,0)+1;}}ksort($Vd);$ne=0;$Hl=0;$sb=0;$ei=null;$Xj=array();$ek=array();foreach($Vd
as$d=>$U){if($ei!==null){$sb=round($sb+$Il[$ei]+1.7+idx($ae,$d,0)*.1,1);$E=array();foreach($U
as$C){$Oj=0;$Kb=0;$Dg=array_keys((array)idx($xi,$C));foreach((array)idx($Hd,$C)as$X)$Dg[]=$X["table"];foreach($Dg
as$yg){if($N[$yg]&&$e[$yg]<$d){$Oj+=$N[$yg]["pos"][0];$Kb++;}}$E[$C]=($Kb?$Oj/$Kb:$ne);}asort($E);$U=array_keys($E);}$Bk=0;foreach($U
as$C){$H=1.25*count($N[$C]["fields"]);$N[$C]["pos"]=($Zj[$C]?:array($Bk,$sb));$Xj[$C]=$N[$C]["pos"][1];$ek[$C]=$Il[$d];$Bk+=2.5+$H;$ne=max($ne,$N[$C]["pos"][0]+2.5+$H);$Hl=max($Hl,round($N[$C]["pos"][1]+$Il[$d],1));if(!$Zj[$C])$ak[]="\n\t'".js_escape($C)."': [ ".$N[$C]["pos"][0].", ".$N[$C]["pos"][1]." ]";}$ei=$d;}$xf=array();$Oa=array();foreach($Hd
as$C=>$tl){foreach($tl
as$X){$kk=idx($Xj,$X["table"],$Xj[$C]);$xj=$Xj[$C]+$ek[$C];$Ii=($kk-1>$xj);$vf=($Ii?$xj+1:min($Xj[$C],$kk)-1);$Na=idx($Oa,(string)$vf,0);$Oa[(string)$vf]=$Na+1;$vf=round($Ii?min($vf+$Na*.1,$kk-1):$vf-$Na*.1,1);while($xf[(string)$vf])$vf-=.0001;$N[$C]["references"][$X["table"]][(string)$vf]=array($X["source"],$X["target"]);$xi[$X["table"]][$C][(string)$vf]=$X["target"];$xf[(string)$vf]=true;}}echo'<div id="schema" style="height: ',$ne,'em; width: ',$Hl,'em;">
<script',nonce(),'>
const tablePos = {',implode(",",$bk)."\n",'};
const tablePosDefault = {',implode(",",$ak)."\n",'};
const em = qs(\'#schema\').offsetHeight / ',$ne,';
document.onmousemove = schemaMousemove;
document.onmouseup = event => schemaMouseup(event, \'',js_escape(DB),'\');
</script>
';foreach($N
as$C=>$S){echo"<div class='table'".on('mousedown','schemaMousedown')." style='top: ".$S["pos"][0]."em; left: ".$S["pos"][1]."em; width: ".$ek[$C]."em;'>",'<a href="'.h(ME).'table='.url_escape($C).'"><b>'.h($C)."</b></a>";foreach($S["fields"]as$k){$X='<span'.type_class($k["type"]).' title="'.h($k["type"].($k["length"]?"($k[length])":"").($k["null"]?" NULL":'')).'">'.h($k["field"]).'</span>';echo"<br>".($k["primary"]?"<i>$X</i>":$X);}foreach((array)$S["references"]as$lk=>$zi){foreach($zi
as$vf=>$ui){$wf=$vf-$S["pos"][1];$Lj=($wf>0?"left: 100%; width: calc($wf"."em - 100%)":"left: $wf"."em");$Hl=($wf>0?"100%":(-$wf)."em");$r=0;foreach($ui[0]as$wj)echo"\n<div class='references' title='".h($lk)."' id='refs$vf-".($r++)."' style='$Lj"."; top: ".$sd[$C][$wj]."em; padding-top: .5em;'>"."<div style='border-top: 1px solid gray; width: $Hl;'></div></div>";}}foreach((array)$xi[$C]as$lk=>$zi){foreach($zi
as$vf=>$mk){$wf=$vf-$S["pos"][1];$r=0;foreach($mk
as$jk)echo"\n<div class='references arrow' title='".h($lk)."' id='refd$vf-".($r++)."' style='left: $wf"."em; top: ".$sd[$C][$jk]."em;'>"."<div style='height: .5em; border-bottom: 1px solid gray; width: ".(-$wf)."em;'></div>"."</div>";}}echo"\n</div>\n";}foreach($N
as$C=>$S){foreach((array)$S["references"]as$lk=>$zi){if($N[$lk]){foreach($zi
as$vf=>$ui){$og=$ne;$Uf=-10;foreach($ui[0]as$x=>$wj){$Vh=$S["pos"][0]+$sd[$C][$wj];$Wh=$N[$lk]["pos"][0]+$sd[$lk][$ui[1][$x]];$og=min($og,$Vh,$Wh);$Uf=max($Uf,$Vh,$Wh);}echo"<div class='references' id='refl$vf' style='left: $vf"."em; top: $og"."em; padding: .5em 0;'><div style='border-right: 1px solid gray; margin-top: 1px; height: ".($Uf-$og)."em;'></div></div>\n";}}}}echo'</div>
<p class="links"><a href="',h(ME."schema=".url_escape($ba)),'" id="schema-link">',lang(158),'</a>
';}elseif(isset($_GET["dump"])){$a=$_GET["dump"];if($_POST&&!$j){$i=array("auto_increment"=>'');foreach(array("type","routine","event","trigger")as$Qj){if(support($Qj))$i[$Qj."s"]='';}save_settings(array_intersect_key($_POST+$i,array_flip(array("output","format","db_style","schema_style","table_style","data_style"))+$i),"adminer_export");$qa=(DB==""||$_GET["ns"]==="");$U=array_flip((array)$_POST["tables"])+array_flip((array)$_POST["data"]);$kd=dump_headers((count($U)==1?key($U):DB),($qa||count($U)>1));$df=preg_match('~sql~',$_POST["format"]);if($df){echo"-- Adminer ".VERSION." ".get_driver(DRIVER)." ".str_replace("\n"," ",connection()->server_info)." dump\n\n";if(JUSH=="sql"){echo"SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
".($_POST["data_style"]?"SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';
":"")."
";connection()->query("SET time_zone = '+00:00'");connection()->query("SET sql_mode = ''");}}$Lj=$_POST["db_style"];$g=array(DB);if(DB==""){$g=$_POST["databases"];if(is_string($g))$g=explode("\n",rtrim(str_replace("\r","",$g),"\n"));}foreach((array)$g
as$h){adminer()->dumpDatabase($h);if(connection()->select_db($h)){if($df&&$Lj)echo
use_sql($h,$Lj).";\n\n";foreach(($_GET["ns"]===""?(array)$_POST["schemas"]:(DB!=""||!support("scheme")?array(""):adminer()->schemas()))as$N){if($N!=""){if(DB==""&&information_schema(DB,$N))continue;set_schema($N);}if($df&&$_POST["schema_style"]&&function_exists('Adminer\use_schema_sql'))echo
use_schema_sql($_GET["ns"],$_POST["schema_style"]).";\n\n";$Hj=($_POST["table_style"]||$_POST["data_style"]?table_status('',true):array());$jd=array();$Wb=array();foreach($Hj
as$C=>$T){if($qa||in_array($C,(array)$_POST["tables"]))$jd[$C]=$T;if($qa||in_array($C,(array)$_POST["data"]))$Wb[$C]=$T;}if($df){if($_POST["table_style"]=="DROP+CREATE"&&function_exists('Adminer\drop_sql'))echo
drop_sql($jd);if($_POST["data_style"]=="TRUNCATE+INSERT"&&function_exists('Adminer\truncate_all_sql')){$Nk=array();foreach($Wb
as$C=>$T){if(!is_view($T)&&!($_POST["table_style"]=="DROP+CREATE"&&isset($jd[$C])))$Nk[]=$C;}echo
truncate_all_sql($Nk);}$sh="";if($_POST["types"]){foreach(types()as$s=>$V){$hc=type_definition($s);$Mg=($hc["kind"]=='d'?"DOMAIN":"TYPE");if($hc["definition"])$sh
.=($Lj!='DROP+CREATE'?"DROP $Mg IF EXISTS ".table($V).";;\n":"")."CREATE $Mg ".table($V)." $hc[definition];\n\n";else$sh
.="-- Could not export type $V\n\n";}}if($_POST["routines"]){foreach(routines()as$L){$C=$L["ROUTINE_NAME"];$Li=$L["ROUTINE_TYPE"];$Lb=create_routine($Li,array("name"=>$C)+routine($L["SPECIFIC_NAME"],$Li));set_utf8mb4($Lb);$sh
.=($Lj!='DROP+CREATE'?"DROP $Li IF EXISTS ".table($C).";;\n":"")."$Lb;\n\n";}}if($_POST["events"]){foreach(get_rows("SHOW EVENTS",null,"-- ")as$L){$Lb=remove_definer(get_val("SHOW CREATE EVENT ".idf_escape($L["Name"]),3));set_utf8mb4($Lb);$sh
.=($Lj!='DROP+CREATE'?"DROP EVENT IF EXISTS ".idf_escape($L["Name"]).";;\n":"")."$Lb;;\n\n";}}echo($sh&&JUSH=='sql'?"DELIMITER ;;\n\n$sh"."DELIMITER ;\n\n":$sh);}if($_POST["table_style"]||$_POST["data_style"]){$zl=array();foreach($Hj
as$C=>$T){$S=array_key_exists($C,$jd);$Ub=array_key_exists($C,$Wb);if($S||$Ub){$zk=null;if($kd=="tar"){$zk=new
TmpFile;ob_start(array($zk,'write'),1e5);}adminer()->dumpTable($C,($S?$_POST["table_style"]:""),(is_view($T)?2:0));if(is_view($T))$zl[]=$C;elseif($Ub){$l=fields($C);$O=array("*");$Hb=convert_fields($l,$l);if($Hb)$O[]=substr($Hb,2);adminer()->dumpData($C,$_POST["data_style"],"",$O);}if($df&&$_POST["triggers"]&&$S&&($Mk=trigger_sql($C)))echo"\nDELIMITER ;;\n$Mk\nDELIMITER ;\n";if($kd=="tar"){ob_end_flush();tar_file((DB!=""?"":"$h/")."$C.csv",$zk);}elseif($df)echo"\n";}}if($df&&$_POST["table_style"]&&function_exists('Adminer\foreign_keys_sql')){foreach($jd
as$C=>$T){if(!is_view($T))echo
foreign_keys_sql($C);}}if($df){foreach($zl
as$yl)adminer()->dumpTable($yl,$_POST["table_style"],1);}if($kd=="tar")echo
pack("x1024");}}}}adminer()->dumpFooter();exit;}page_header(lang(76),$j,($_GET["export"]!=""?array("table"=>$_GET["export"]):array()),h(DB));echo'
<form action="" method="post">
<table class="layout">
';$ac=array('','USE','DROP+CREATE','CREATE');$Qi=(JUSH=="mssql"?array('','DROP+CREATE','CREATE'):$ac);$dk=array('','DROP+CREATE','CREATE');$Vb=array('','TRUNCATE+INSERT','INSERT');if(JUSH=="sql")$Vb[]='INSERT+UPDATE';$L=get_settings("adminer_export");if(!$L)$L=array("output"=>"text","format"=>"sql","db_style"=>(DB!=""?"":"CREATE"),"schema_style"=>"","table_style"=>"DROP+CREATE","data_style"=>"INSERT");echo"<tr><th>".lang(159)."<td>".html_radios("output",adminer()->dumpOutput(),$L["output"])."\n","<tr><th>".lang(160)."<td>".html_radios("format",adminer()->dumpFormat(),$L["format"])."\n",(JUSH=="sqlite"?"":"<tr><th>".lang(35)."<td>".html_select('db_style',$ac,$L["db_style"]).(support("type")?checkbox("types",1,$L["types"],lang(0)):"").(support("routine")?checkbox("routines",1,$L["routines"],lang(72)):"").(support("event")?checkbox("events",1,$L["events"],lang(74)):"")),(function_exists('Adminer\use_schema_sql')?"<tr><th>".lang(161)."<td>".html_select('schema_style',$Qi,$L["schema_style"]):""),"<tr><th>".lang(134)."<td>".html_select('table_style',$dk,$L["table_style"]).checkbox("auto_increment",1,$L["auto_increment"],lang(51)).(support("trigger")?checkbox("triggers",1,$L["triggers"],lang(153)):""),"<tr><th>".lang(162)."<td>".html_select('data_style',$Vb,$L["data_style"]),'</table>
';adminer()->dumpPrint();echo'<p><input type=\'submit\' value=\'',lang(76),'\'>
',input_token(),'
<table',on('click','dumpClick'),'>
';$di=array();if($_GET["ns"]===""&&support("scheme")){echo"<thead><tr><th style='text-align: left;'>","<label class='block'><input type='checkbox' id='check-schemas' checked class='jsonly' title='".lang(163)."'".on('click','formCheck','^schemas\[').">".lang(161)."</label>","<tbody>\n";foreach(adminer()->schemas()as$N){if(!information_schema(DB,$N))echo"<tr><td>".checkbox("schemas[]",$N,true,$N,"","block")."\n";}}elseif(DB!=""){$gb=($a!=""?"":" checked");echo"<thead><tr>","<th style='text-align: left;'><label class='block'><input type='checkbox' id='check-tables'$gb class='jsonly' title='".lang(163)."'".on('click','formCheck','^tables\[').">".lang(143)."</label>","<th style='text-align: right;'><label class='block'>".lang(162)."<input type='checkbox' id='check-data'$gb class='jsonly' title='".lang(163)."'".on('click','formCheck','^data\[')."></label>","<tbody>\n";$zl="";$gk=tables_list();foreach($gk
as$C=>$V){$ci=preg_replace('~_.*~','',$C);$gb=($a==""||$a==(substr($a,-1)=="%"?"$ci%":$C));$hi="<tr><td>".checkbox("tables[]",$C,$gb,$C,"","block");if($V!==null&&!preg_match('~table~i',$V))$zl
.="$hi\n";else
echo"$hi<td align='right'><label class='block'><span id='Rows-".h($C)."'></span>".checkbox("data[]",$C,$gb)."</label>\n";$di[$ci]++;}echo$zl;if($gk)echo
script("ajaxSetHtml('".js_escape(ME)."script=db');");}else{$g=adminer()->databases();echo"<thead><tr><th style='text-align: left;'>","<label class='block'>".($g?"<input type='checkbox' id='check-databases'".($a==""?" checked":"")." class='jsonly' title='".lang(163)."'".on('click','formCheck','^databases\[').">":"").lang(35)."</label>","<tbody>\n";if($g){foreach($g
as$h){if(!information_schema($h)){$ci=preg_replace('~_.*~','',$h);echo"<tr><td>".checkbox("databases[]",$h,$a==""||$a=="$ci%",$h,"","block")."\n";$di[$ci]++;}}}else
echo"<tr><td><textarea name='databases' rows='10' cols='20'></textarea>";}echo'</table>
</form>
';$Ad=true;foreach($di
as$x=>$X){if($x!=""&&$X>1){echo($Ad?"<p>":" ")."<a href='".h(ME)."dump=".url_escape("$x%")."'>".h($x)."</a>";$Ad=false;}}}elseif(isset($_GET["sql"])){if(!$j&&$_POST["export"]){save_settings(array("output"=>$_POST["output"],"format"=>$_POST["format"]),"adminer_import");dump_headers("sql");if($_POST["format"]=="sql")echo"$_POST[query]\n";else{adminer()->dumpTable("","");adminer()->dumpData("","table",$_POST["query"]);adminer()->dumpFooter();}exit;}if(!$j&&$_POST["val"]){$ma=0;$Mj=true;$Za=array();$Oi=0;foreach($_POST["val"]as$M)$Oi+=count($M);$Qa=$Oi>1&&driver()->begin();foreach($_POST["val"]as$Vj=>$M){$S=bracket_escape($Vj,true);$l=fields($S);$Wj=indexes($S);foreach($M
as$t=>$L){parse_str(bracket_escape($t,true),$Z);$Vk=array();foreach($Z["where"]as$x=>$X)$Vk[bracket_escape($x,true)]=$X;if(!$l||$Z["null"]||array_diff_key($Vk,$l)||!unique_array($Vk,$Wj)){$Mj=false;break
2;}$Q=array();$O=array();foreach($L
as$jf=>$X){$x=bracket_escape($jf,true);$k=idx($l,$x);if(!$k){$Mj=false;break
3;}$Q[idf_escape($x)]=(preg_match('~char|text~',$k["type"])||$X!=""?adminer()->processInput($k,$X):"NULL");$O[$jf]=$x;}$qi=where($Z,$l);if(!driver()->update($S,$Q," WHERE $qi",0," ")){$Mj=false;break
2;}$ma+=connection()->affected_rows;$e=array();foreach($O
as$x)$e[]=idf_escape($x);$el=driver()->select($S,$e,array($qi),$e);$Fg=($el?$el->fetch_row():array());$w=0;foreach($O
as$jf=>$x){$k=$l[$x];$Kj=array('type'=>(preg_match('~binary~',$k["type"])?'blob':$k["type"]));$Za["val[$Vj][$t][$jf]"]=select_value(idx($Fg,$w++),"",$Kj,null);}}}if($Qa&&$Mj)$Mj=driver()->commit();queries_redirect(null,lang(164,$ma),$Mj);if($Qa&&!$Mj)driver()->rollback();page_headers();page_messages($j);foreach($Za
as$C=>$X)echo"<div data-name='".h($C)."' hidden>$X</div>\n";exit;}restart_session();$te=&get_session("queries");$se=&$te[DB];if(!$j&&$_POST["clear"]){$se=array();redirect(remove_from_uri("history"));}stop_session();$ka=get_settings("adminer_import");if($_POST&&$ka)save_settings($ka,"adminer_import");page_header((isset($_GET["import"])?lang(75):lang(64)),$j);$Ef=driver()->lineComment();if(!$j&&$_POST&&!(isset($_GET["import"])&&adminer()->importProcess())){$ic=driver()->delimiter;$o=false;if(!isset($_GET["import"]))$I=$_POST["query"];elseif($_POST["webfile"]){$_j=adminer()->importServerPath();$o=@fopen((file_exists($_j)?$_j:"compress.zlib://$_j.gz"),"rb");$I=($o?fread($o,1e6):false);}else$I=get_file("sql_file",true,$ic);if(is_string($I)){if(($cg=ini_bytes("memory_limit"))!="-1")ini_set("memory_limit",max($cg,strval(2*strlen($I)+memory_get_usage()+8e6)));if($I!=""&&strlen($I)<1e6){$ni=$I.(preg_match("~$ic\\s*\$~",$I)?"":$ic);if(!$se||first(end($se))!=$ni){restart_session();$se[]=array($ni,time());set_session("queries",$te);stop_session();}}$yj="(?:\\s|\xEF\xBB\xBF|/\\*[\s\S]*?\\*/|(?:$Ef)[^\n]*\n?|--\r?\n)";$Sg=0;$Qc=true;$Jb=false;$f=connect();if($f&&DB!=""){$f->select_db(DB);if($_GET["ns"]!="")set_schema($_GET["ns"],$f);}$tb=0;$Yc=array();$_h='[\'"'.(JUSH=="sql"?'`':(JUSH=="sqlite"?'`[':(JUSH=="mssql"?'[':''))).']|/\*|'.$Ef.'|$'.(JUSH=="pgsql"?'|\$([a-zA-Z]\w*)?\$':'');$Ck=microtime(true);while($I!=""){if(!$Sg&&preg_match("~^$yj*+DELIMITER\\s+(\\S+)~i",$I,$B)){$ic=preg_quote($B[1]);$I=substr($I,strlen($B[0]));}elseif(!$Sg&&JUSH=='pgsql'&&preg_match("~^($yj*+COPY\\s+)[^;]+\\s+FROM\\s+stdin;~i",$I,$B)){$ic="\n\\\\\\.\r?\n";$Jb=true;$Sg=strlen($B[0]);}else{preg_match("($ic\\s*|$_h)",$I,$B,PREG_OFFSET_CAPTURE,$Sg);list($Jd,$H)=$B[0];if(!$Jd&&$o&&!feof($o))$I
.=fread($o,1e5);else{if(!$Jd&&rtrim($I)=="")break;$Sg=$H+strlen($Jd);if($Jd&&!preg_match("(^$ic)",$Jd)){$Xa=driver()->hasCStyleEscapes()||(JUSH=="pgsql"&&($H>0&&strtolower($I[$H-1])=="e"));$Mh=($Jd=='/*'?'\*/':($Jd=='['?']':(preg_match("~^(?:$Ef)~",$Jd)?"\n":preg_quote($Jd).($Xa?'|\\\\.':''))));while(preg_match("($Mh|\$)s",$I,$B,PREG_OFFSET_CAPTURE,$Sg)){$Pi=$B[0][0];if(!$Pi&&$o&&!feof($o))$I
.=fread($o,1e5);else{$Sg=$B[0][1]+strlen($Pi);if(!$Pi||$Pi[0]!="\\")break;}}}else{$ni=substr($I,0,$H+($Jb?3:0));$I=substr($I,$Sg);$Sg=0;if($Jb){$ic=driver()->delimiter;$Jb=false;}$nb="<code class='jush-".JUSH."'>".adminer()->sqlCommandQuery($ni)."</code>";if(preg_match("~^$yj*+\$~",$ni)&&!preg_match('~/\*M?!~',$ni)){echo($_POST["only_errors"]?"":"<pre>$nb</pre>\n");continue;}$Qc=false;$tb++;$hi="<pre id='sql-$tb'>$nb</pre>\n";if(JUSH=="sqlite"&&preg_match("~^$yj*+(ATTACH|VACUUM\\b.*\\bINTO)\\b~is",$ni,$B)!==0){echo$hi,"<p class='error'>".lang(165,preg_match('~ATTACH~i',$B[1])?'ATTACH':'VACUUM INTO')."\n";$Yc[]=" <a href='#sql-$tb'>$tb</a>";if($_POST["error_stops"])break;}else{if(!$_POST["only_errors"]){echo$hi;ob_flush();flush();}$Dj=microtime(true);if(connection()->multi_query($ni)&&$f&&preg_match("~^$yj*+USE\\b~i",$ni))$f->query($ni);do{$J=connection()->store_result();if(connection()->error){echo($_POST["only_errors"]?$hi:""),"<p class='error'>".lang(166).(connection()->errno?" (".connection()->errno.")":"").": ".adminer()->error()."\n";$Yc[]=" <a href='#sql-$tb'>$tb</a>";if($_POST["error_stops"])break
2;}else{$_=ME."sql=".url_escape(trim($ni));$sk=" <span class='time'>(".format_time($Dj).")</span>".(strlen($_)<1900?" <a href='".h($_)."'>".lang(14)."</a>":"");$ma=connection()->affected_rows;$Bl=($_POST["only_errors"]?"":driver()->warnings());$Cl="warnings-$tb";if($Bl)$sk
.=", <a href='#$Cl' class='toggle'>".lang(46)."</a>";$hd="";$id="explain-$tb";if(is_object($J)){$z=$_POST["limit"];$Lg=$z;$Jc=!$_POST["only_errors"];if($Jc)echo"<form action='' method='post'>\n";$jh=print_select_result($J,$f,array(),$Lg,$Jc);if(!$_POST["only_errors"]){$Lg=max($J->num_rows,$Lg);echo"<p class='sql-footer'>".($Lg?($z&&$Lg>$z?lang(167,$z):"").lang(168,$Lg):""),$sk;if($f&&preg_match("~^($yj|\\()*+SELECT\\b~i",$ni)&&($hd=adminer()->explain($f,$ni,$jh))!="")echo", <a href='#$id' class='toggle'>Explain</a>";if($Jc)echo", <input type='submit' name='save' value='".lang(18)."' class='jsonly' disabled"." title='".lang(169)."'".on('click','sqlSave',lang(21)).">";$s="export-$tb";echo", <a href='#$s' class='toggle'>".lang(76)."</a><span id='$s' class='hidden'>: ".html_select("output",adminer()->dumpOutput(),$ka["output"])." ".html_select("format",adminer()->dumpFormat(),$ka["format"]).input_hidden("query",$ni)."<input type='submit' name='export' value='".lang(76)."'".($z?"":on('click','sqlExport')).">".input_token()."</span>\n"."</form>\n";}}else{if(preg_match("~^$yj*+(CREATE|DROP|ALTER)$yj++(DATABASE|SCHEMA)\\b~i",$ni)){restart_session();set_session("dbs",null);stop_session();}if(!$_POST["only_errors"])echo"<p class='message' title='".h(connection()->info)."'>".lang(170,$ma)."$sk\n";}echo($Bl?"<div id='$Cl' class='hidden'>\n$Bl</div>\n":""),($hd!=""?"<div id='$id' class='hidden explain'>\n$hd</div>\n":"");}$Dj=microtime(true);}while(connection()->next_result());}}}}}if($Qc)echo"<p class='message'>".lang(171)."\n";else{$Fe=connection()->inTransaction();driver()->rollback();if($Fe)echo"<pre><code class='jush-".JUSH."'>ROLLBACK".(JUSH=="mssql"?" TRANSACTION":"")." -- Adminer</code></pre>\n";if($_POST["only_errors"])echo"<p class='message'>".lang(172,$tb-count($Yc))," <span class='time'>(".format_time($Ck).")</span>\n";elseif($Yc&&$tb>1)echo"<p class='error'>".lang(166).": ".implode("",$Yc)."\n";}}else
echo"<p class='error'>".upload_error($I)."\n";}echo'
<form action="" method="post" enctype="multipart/form-data" id="form"';$fl="";if(!isset($_GET["import"]))echo
on('submit','sqlSubmit',remove_from_uri("sql|limit|error_stops|only_errors|history"));else
echo
on_upload_progress($fl);echo'>
';$ed="<input type='submit' value='".lang(173)."' title='Ctrl+Enter'>";if(!isset($_GET["import"])){$ni=$_GET["sql"];if($_POST)$ni=$_POST["query"];elseif($_GET["history"]=="all")$ni=$se;elseif($_GET["history"]!="")$ni=idx($se[$_GET["history"]],0);echo"<p>";textarea("query",$ni,20);echo($_POST?"":script("qs('textarea').focus();")),"<p>";adminer()->sqlPrintAfter();echo"$ed\n",lang(174).": <input type='number' name='limit' class='size' value='".h($_POST?$_POST["limit"]:$_GET["limit"])."'>\n";}else{$be=(extension_loaded("zlib")?"[.gz]":"");echo"<fieldset><legend>".lang(175)."</legend><div>",($fl?input_hidden(ini_get("session.upload_progress.name"),$fl):""),"SQL$be: ".file_input(" name='sql_file[]' multiple","\n$ed"),($fl?" <progress class='jsonly hidden' max='1' value='0'></progress>":""),"</div></fieldset>\n";$De=adminer()->importServerPath();if($De)echo"<fieldset><legend>".lang(176)."</legend><div>",lang(177,"<code>".h($De)."$be</code>")," <input type='submit' name='webfile' value='".lang(178)."'>","</div></fieldset>\n";adminer()->importPrint();echo"<p>";}echo
checkbox("error_stops",1,($_POST?$_POST["error_stops"]:isset($_GET["import"])||$_GET["error_stops"]),lang(179))."\n",checkbox("only_errors",1,($_POST?$_POST["only_errors"]:isset($_GET["import"])||$_GET["only_errors"]),lang(180))."\n",input_token();if(!isset($_GET["import"])&&$se){print_fieldset("history",lang(181),$_GET["history"]!="");for($X=end($se);$X;$X=prev($se)){$x=key($se);list($ni,$sk,$Mc)=$X;echo'<div><a href="'.h(ME."sql=&history=$x").'" class="hover">'.lang(14)."</a>"." <span class='time' title='".@date('Y-m-d',$sk)."'>".@date("H:i:s",$sk)."</span>"." <code class='jush-".JUSH."'>".shorten_utf8(preg_replace('~\s+~',' ',ltrim(preg_replace("~^(?:$Ef).*~m",'',$ni))),80,"</code>").($Mc?" <span class='time'>($Mc)</span>":"")."</div>\n";}echo"<input type='submit' name='clear' value='".lang(182)."'>\n","<a href='".h(ME."sql=&history=all")."'>".lang(183)."</a>\n","</div></fieldset>\n";}echo'</form>
';}elseif(isset($_GET["edit"])){$a=$_GET["edit"];$l=fields($a);$Z=(isset($_GET["select"])?($_POST["check"]&&count($_POST["check"])==1?where_check($_POST["check"][0],$l):""):where($_GET,$l));$cl=(isset($_GET["select"])?$_POST["edit"]:$Z);foreach($l
as$C=>$k){if((!$cl&&!isset($k["privileges"]["insert"]))||adminer()->fieldName($k)=="")unset($l[$C]);}if($_POST&&!$j&&!isset($_GET["select"])){$A=relative_uri((string)$_POST["referer"]);if($_POST["insert"])$A=($cl?null:relative_uri());elseif(!preg_match('~^.+&select=.+$~',$A))$A=ME."select=".url_escape($a);$v=indexes($a);$Wk=unique_array($_GET["where"],$v);$qi="\nWHERE $Z";if(isset($_POST["delete"]))queries_redirect($A,lang(184),driver()->delete($a,$qi,$Wk?0:1));else{$Q=array();foreach($l
as$C=>$k){$X=process_input($k);if($X!==false&&$X!==null)$Q[idf_escape($C)]=$X;}if($cl){if(!$Q)redirect($A);queries_redirect($A,lang(185),driver()->update($a,$Q,$qi,$Wk?0:1));if(is_ajax()){page_headers();page_messages($j);exit;}}else{$J=driver()->insert($a,$Q);$uf=($J?last_id($J):0);queries_redirect($A,lang(186,($uf?" $uf":"")),$J);}}}$L=null;$I="";$sk="";if($Z){$O=array();$Zi=array("*");foreach($l
as$C=>$k){if(isset($k["privileges"]["select"])){$Aa=($_POST["clone"]&&$k["auto_increment"]?"''":convert_field($k));$d=($Aa?"$Aa AS ":"").idf_escape($C);$O[]=$d;if($Aa)$Zi[]=$d;}}$L=array();if(!support("table")){$O=array("*");$Zi=$O;}if($O){$Dj=microtime(true);$J=driver()->select($a,$O,array($Z),$O,array(),(isset($_GET["select"])?2:1));$I=str_replace("SELECT ".implode(", ",$O),"SELECT ".implode(", ",$Zi),driver()->query);$sk=format_time($Dj);if(!$J)$j=adminer()->error();else{$L=$J->fetch_assoc();if(!$L)$L=false;}if(isset($_GET["select"])&&(!$L||$J->fetch_assoc()))$L=null;}}if(!$l&&driver()->primary!=""){if(!$Z){$J=driver()->select($a,array("*"),array(),array("*"));$L=($J?$J->fetch_assoc():false);if(!$L)$L=array(driver()->primary=>"");}if($L){foreach($L
as$x=>$X){if(!$Z)$L[$x]=null;$l[$x]=array("field"=>$x,"null"=>($x!=driver()->primary),"auto_increment"=>($x==driver()->primary));}}}if($_POST["save"]){$Xh=array();foreach((array)$_POST["fields"]as$x=>$X)$Xh[bracket_escape($x,true)]=$X;$L=$Xh+($L?$L:array());}edit_form($a,$l,$L,$cl,$j,$I,$sk);}elseif(isset($_GET["create"])){function
referencable_primary($bj){$K=array();foreach(table_status('',true)as$Yj=>$S){if($Yj!=$bj&&!$S["dependent"]&&fk_support($S)){foreach(fields($Yj)as$k){if($k["primary"]){if($K[$Yj]){unset($K[$Yj]);break;}$K[$Yj]=$k;}}}}return$K;}$a=$_GET["create"];$Dh=driver()->partitionBy;$Gh=($Dh&&$a!=""?driver()->partitionsInfo($a):array());$wi=referencable_primary($a);$Hd=array();foreach($wi
as$Yj=>$k)$Hd[str_replace("`","``",$Yj)."`".str_replace("`","``",$k["field"])]=$Yj;$mh=array();$T=array();$Jg=false;if($a!=""){$mh=fields($a);$T=table_status1($a);$Jg=(count($T)<2);}$ua=($a==""||driver()->supportsAlterTable($T));$L=$_POST;$L["fields"]=(array)$L["fields"];if($L["auto_increment_col"])$L["fields"][$L["auto_increment_col"]]["auto_increment"]=true;if($_POST&&!$j)save_settings(array("comments"=>$_POST["comments"],"defaults"=>$_POST["defaults"]));if($_POST&&!process_fields($L["fields"])&&!$j){if($_POST["drop"])queries_redirect(substr(ME,0,-1),lang(187),drop_tables(array($a)));else{$l=array();$ra=array();$jl=false;$Fd=array();$lh=reset($mh);$oa=" FIRST";foreach($L["fields"]as$k){$n=$Hd[$k["type"]];$Pk=($n!==null?$wi[$n]:$k);if($k["field"]!=""){if(!$k["generated"])$k["default"]=null;$li=process_field($k,$Pk);$ra[]=array($k["orig"],$li,$oa);if(!$lh||$li!==process_field($lh,$lh)){$l[]=array($k["orig"],$li,$oa);if($k["orig"]!=""||$oa)$jl=true;}if($n!==null)$Fd[idf_escape($k["field"])]=($a!=""&&JUSH!="sqlite"?"ADD":" ").format_foreign_key(array('table'=>$Hd[$k["type"]],'source'=>array($k["field"]),'target'=>array($Pk["field"]),'on_delete'=>$k["on_delete"],),object_name("FOREIGN",trim($L["name"]),array($k["field"])));$oa=" AFTER ".idf_escape($k["field"]);}elseif($k["orig"]!=""){$jl=true;$l[]=array($k["orig"]);}if($k["orig"]!=""){$lh=next($mh);if(!$lh)$oa="";}}$Fh=array();if(in_array($L["partition_by"],$Dh)){foreach($L
as$x=>$X){if(preg_match('~^partition~',$x))$Fh[$x]=$X;}foreach($Fh["partition_names"]as$x=>$C){if($C==""){unset($Fh["partition_names"][$x]);unset($Fh["partition_values"][$x]);}}$Fh["partition_names"]=array_values($Fh["partition_names"]);$Fh["partition_values"]=array_values($Fh["partition_values"]);if($Fh==$Gh)$Fh=array();}elseif(preg_match("~partitioned~",$T["Create_options"]))$Fh=null;$eg=lang(188);if($a==""){cookie("adminer_engine",$L["Engine"]);$eg=lang(189);}$C=trim($L["name"]);$A=ME.(support("table")?"table=":"select=").url_escape($C);$J=alter_table($a,$C,(JUSH=="sqlite"&&($jl||$Fd)?$ra:$l),$Fd,($L["Comment"]!=$T["Comment"]?$L["Comment"]:null),($L["Engine"]&&$L["Engine"]!=$T["Engine"]?$L["Engine"]:""),($L["Collation"]&&$L["Collation"]!=$T["Collation"]?$L["Collation"]:""),($L["Auto_increment"]!=""?number($L["Auto_increment"]):""),$Fh);if($J&&!Queries::$queries&&$a!=""&&!$l&&!$Fd)redirect($A);queries_redirect($A,$eg,$J);}}$Ej=($a!=""?"alter":"create");page_header(($a!=""?lang(44):lang(77)),$j,array("table"=>$a),h($a),$Jg,doc_link(array('sqlite'=>"lang_{$Ej}table.html",)));if(!$_POST){$Sk=driver()->types();$L=array("Engine"=>$_COOKIE["adminer_engine"],"fields"=>array(array("field"=>"","type"=>(isset($Sk["int"])?"int":(isset($Sk["integer"])?"integer":"")),"on_update"=>"")),"partition_names"=>array(""),);if($a!=""){$L=$T;$L["name"]=$a;$L["fields"]=array();if(!$_GET["auto_increment"])$L["Auto_increment"]="";foreach($mh
as$k){if($k["generated"])$k["default"]=ltrim($k["default"]);$k["generated"]=$k["generated"]?:(isset($k["default"])?"DEFAULT":"");$L["fields"][]=$k;}if($Dh){$L+=$Gh;$L["partition_names"][]="";$L["partition_values"][]="";}}}$qb=flat_collations();$Tc=driver()->engines();foreach($Tc
as$Sc){if(!strcasecmp($Sc,$L["Engine"])){$L["Engine"]=$Sc;break;}}$Pf=max_input_vars(12,20);if($Pf){$pe=(count($L["fields"])>$Pf?"":" hidden");echo"<p".($pe?" id='max-fields' data-columns='$Pf'":"")." class='error$pe'>".max_input_vars_error()."\n";}echo'
<form action="" method="post" id="form">
<p>
';if(support("columns")||$a==""){echo
lang(190).": <input name='name'".($a==""&&!$_POST?" autofocus":"")." data-maxlength='64' value='".h($L["name"])."' autocapitalize='off'>\n",(!$ua?h($T["Engine"])."\n":($Tc?html_select("Engine",array(""=>"(".lang(191).")")+$Tc,$L["Engine"],on('change','helpClose').on_help_value())."\n":""));if($qb)echo"<datalist id='collations'>".optionlist($qb)."</datalist>\n",(preg_match("~sqlite|mssql~",JUSH)?"":"<input list='collations' name='Collation' value='".h($L["Collation"])."' placeholder='(".lang(113).")'>\n");echo"<input type='submit' value='".lang(18)."'>\n";}if(support("columns")&&$ua){echo"<div class='scrollable'>\n","<table id='edit-fields' class='nowrap'>\n";edit_fields($L["fields"],$qb,"TABLE",$Hd);echo"</table>\n",script("editFields();"),"</div>\n<p>\n",lang(51).": <input type='number' name='Auto_increment' class='size' value='".h($L["Auto_increment"])."'>\n",checkbox("defaults",1,($_POST?$_POST["defaults"]:get_setting("defaults")),lang(192),on('click','columnShowClick',6),"jsonly");$wb=($_POST?$_POST["comments"]:get_setting("comments"));if(support("comment")){echo
checkbox("comments",1,$wb,lang(50),on('click','editingCommentsClick',true),"jsonly").' ';$b=" name='Comment' data-maxlength='".(min_version(5.5)?2048:60)."'".($wb?"":" class='hidden'");echo
adminer()->commentInput('TABLE',$b,$L["Comment"]);}echo'<p>
<input type=\'submit\' value=\'',lang(18),'\'>
';}echo'
';if($a!="")echo'<input type=\'submit\' name=\'drop\' value=\'',lang(138),'\'',confirm(lang(193,$a)),'>
';if($Dh&&(JUSH=='sql'||$a=="")){$Eh=preg_match('~RANGE|LIST~',$L["partition_by"]);print_fieldset("partition",lang(194),$L["partition_by"]);echo"<p>".html_select("partition_by",array_merge(array(""),$Dh),$L["partition_by"],on('change','partitionByChange').on_help_value('.','PARTITION BY $&'))."\n","(<input name='partition' value='".h($L["partition"])."'>)\n",lang(195).": <input type='number' name='partitions' class='size".($Eh||!$L["partition_by"]?" hidden":"")."' value='".h($L["partitions"])."'>\n","<table id='partition-table'".($Eh?"":" class='hidden'").">\n","<thead><tr><th>".lang(196)."<th>".lang(197)."<tbody>\n";foreach($L["partition_names"]as$x=>$X)echo'<tr>','<td><input name="partition_names[]" value="'.h($X).'" autocapitalize="off"'.($x==count($L["partition_names"])-1?on('input','partitionNameChange'):'').'>','<td><input name="partition_values[]" value="'.h(idx($L["partition_values"],$x)).'">';echo"</table>\n</div></fieldset>\n";}echo
input_token(),'</form>
';}elseif(isset($_GET["indexes"])){$a=$_GET["indexes"];$Ke=array("PRIMARY","UNIQUE","INDEX");$T=table_status1($a,true);$Ie=driver()->indexAlgorithms($T);if(preg_match('~MyISAM|M?aria'.(min_version(5.6,'10.0.5')?'|InnoDB':'').'~i',$T["Engine"]))$Ke[]="FULLTEXT";if(preg_match('~MyISAM|M?aria'.(min_version(5.7,'10.2.2')?'|InnoDB':'').'~i',$T["Engine"]))$Ke[]="SPATIAL";if(min_version('',11.7)&&preg_match('~MyISAM|InnoDB~i',$T["Engine"]))$Ke[]="VECTOR";$v=indexes($a);$l=fields($a);$fi=array();if(JUSH=="mongo"){$fi=$v["_id_"];unset($Ke[0]);unset($v["_id_"]);}$L=$_POST;if($L)save_settings(array("index_options"=>$L["options"]));if($_POST&&!$j&&!$_POST["add"]&&!$_POST["drop_col"]){$ta=array();foreach($L["indexes"]as$u){$C=$u["name"];if(in_array($u["type"],$Ke)){$e=array();$Af=array();$lc=array();$Zg=array();$Je=(support("partial_indexes")?$u["partial"]:"");$He=(in_array($u["algorithm"],$Ie)?$u["algorithm"]:"");$Q=array();ksort($u["columns"]);foreach($u["columns"]as$x=>$d){if($d!=""){$y=idx($u["lengths"],$x);$jc=idx($u["descs"],$x);$Yg=idx($u["opclasses"],$x);$Q[]=($l[$d]?idf_escape($d):$d).($y?"(".(+$y).")":"").($Yg!=""?" ".idf_escape($Yg):"").($jc?" DESC":"");$e[]=$d;$Af[]=($y?:null);$lc[]=$jc;$Zg[]="$Yg";}}$fd=$v[$C];if($fd){ksort($fd["columns"]);ksort($fd["lengths"]);ksort($fd["descs"]);if($u["type"]==$fd["type"]&&array_values($fd["columns"])===$e&&(!$fd["lengths"]||array_values($fd["lengths"])===$Af)&&array_values($fd["descs"])===$lc&&(!$fd["opclasses"]||array_values($fd["opclasses"])===$Zg)&&$fd["partial"]==$Je&&(!$Ie||$fd["algorithm"]==$He)){unset($v[$C]);continue;}}if($e)$ta[]=array($u["type"],$C,$Q,$He,$Je);}}foreach($v
as$C=>$fd)$ta[]=array($fd["type"],$C,"DROP");if(!$ta)redirect(ME."table=".url_escape($a));queries_redirect(ME."table=".url_escape($a),lang(198),alter_indexes($a,$ta));}page_header(lang(145),$j,array("table"=>$a),h($a),false,doc_link(array('sqlite'=>"lang_createindex.html",)));$ud=array_keys($l);if($_POST["add"]){foreach($L["indexes"]as$x=>$u){if($u["columns"][count($u["columns"])]!="")$L["indexes"][$x]["columns"][]="";}$u=end($L["indexes"]);if($u["type"]||array_filter($u["columns"],'strlen'))$L["indexes"][]=array("columns"=>array(1=>""));}if(!$L){foreach($v
as$x=>$u){$v[$x]["name"]=$x;$v[$x]["columns"][]="";}$v[]=array("columns"=>array(1=>""));$L["indexes"]=$v;}$Af=(JUSH=="sql"||JUSH=="mssql");$Zg=driver()->indexOpclasses();$pj=($_POST?$_POST["options"]:get_setting("index_options"));$_g=array();foreach($Ke
as$V)$_g[$V]=str_replace("{table}",$a,adminer()->namePattern($V));echo'
<form action="" method="post">
<div class="scrollable">
<table class="nowrap odds">
<thead><tr>
<th id="label-type">',lang(199);$Be=" class='idxopts".($pj?"":" hidden")."'";if($Ie)echo"<th id='label-algorithm'$Be>".lang(200).'';echo'<th><input type="submit" hidden>',lang(201).($Af?"<span$Be> (".lang(202).")</span>":"");if($Af||support("descidx"))echo
checkbox("options",1,$pj,lang(119),on('click','indexOptionsShow'),"jsonly")."\n";echo'<th id="label-name">',lang(203);if(support("partial_indexes"))echo"<th id='label-condition'$Be>".lang(204);echo'<td><noscript>',icon("plus","add[0]","+",lang(120)),'</noscript>
<tbody>
';if($fi){echo"<tr><td>PRIMARY<td>";foreach($fi["columns"]as$x=>$d)echo
select_input(" disabled",array_combine($ud,$ud),$d),"<label><input disabled type='checkbox'>".lang(59)."</label> ";echo"<td><td>\n";}$w=1;foreach($L["indexes"]as$u){if(!$_POST["drop_col"]||$w!=key($_POST["drop_col"])){echo"<tr><td>".html_select("indexes[$w][type]",array(-1=>"")+$Ke,$u["type"],on('change','indexesChangeType',$_g),"label-type");if($Ie)echo"<td$Be>".html_select("indexes[$w][algorithm]",array_merge(array(""),$Ie),$u['algorithm'],"","label-algorithm");echo"<td>";ksort($u["columns"]);$r=1;foreach($u["columns"]as$x=>$d){echo"<span>".select_input(" name='indexes[$w][columns][$r]' title='".lang(48)."'".on('change','indexesChangeColumn',$_g),($l&&($d==""||$l[$d])?array_combine($ud,$ud):array()),$d)," <span$Be>",($Af?"<input type='number' name='indexes[$w][lengths][$r]' class='size' value='".h(idx($u["lengths"],$x))."' title='".lang(118)."'>":"");if($Zg){$Yg=idx($u["opclasses"],$x);echo
html_select("indexes[$w][opclasses][$r]",array(""=>"(".lang(205).")")+array_combine($Zg,$Zg)+($Yg!=""?array($Yg=>$Yg):array()),$Yg),'';}echo(support("descidx")?checkbox("indexes[$w][descs][$r]",1,idx($u["descs"],$x),lang(59)):""),"<br>","</span></span>";$r++;}echo"<td><input name='indexes[$w][name]' value='".h($u["name"])."' autocapitalize='off' aria-labelledby='label-name'>\n";if(support("partial_indexes"))echo"<td$Be><input name='indexes[$w][partial]' value='".h($u["partial"])."' autocapitalize='off' aria-labelledby='label-condition'>\n";echo"<td>".icon("cross","drop_col[$w]","x",lang(122),on('click','editingRemoveRow','indexes$1[type]'));}$w++;}echo'</table>
</div>
<p>
<input type=\'submit\' value=\'',lang(18),'\'>
',input_token(),'</form>
';}elseif(isset($_GET["database"])){$L=$_POST;if($_POST&&!$j&&!$_POST["add"]){$C=trim($L["name"]);if($_POST["drop"]){$_GET["db"]="";queries_redirect(remove_from_uri("db|database"),lang(206),drop_databases(array(DB)));}elseif($C!==DB){if(DB!=""){$_GET["db"]=$C;queries_redirect(preg_replace('~\bdb=[^&]*&~','',ME)."db=".url_escape($C),lang(207),rename_database($C,(string)$L["collation"]));}else{$g=explode("\n",str_replace("\r","",$C));$Mj=true;$sf="";foreach($g
as$h){if(count($g)==1||$h!=""){if(!create_database($h,(string)$L["collation"]))$Mj=false;$sf=$h;}}restart_session();set_session("dbs",null);queries_redirect(preg_replace('~&db=[^&]*~','',ME)."db=".url_escape($sf),lang(208),$Mj);}}else{if(!$L["collation"])redirect(substr(ME,0,-1));query_redirect("ALTER DATABASE ".idf_escape($C).(preg_match('~^[a-z0-9_]+$~i',$L["collation"])?" COLLATE $L[collation]":""),substr(ME,0,-1),lang(209));}}$Ej=(DB!=""?"alter":"create");page_header(DB!=""?lang(67):lang(126),$j,array(),h(DB),false,doc_link(array()));$qb=collations();$C=DB;if($_POST)$C=$L["name"];elseif(DB!="")$L["collation"]=db_collation(DB,$qb);elseif(JUSH=="sql"){foreach(get_vals("SHOW GRANTS")as$Ud){if(preg_match('~ ON (`(([^\\\\`]|``|\\\\.)*)%`\.\*)?~',$Ud,$B)&&$B[1]){$C=stripcslashes(idf_unescape("`$B[2]`"));break;}}}echo'
<form action="" method="post">
<p>
',($_POST["add"]||strpos($C,"\n")?'<textarea autofocus name="name" rows="10" cols="40">'.h($C).'</textarea><br>':'<input name="name" autofocus value="'.h($C).'" data-maxlength="64" autocapitalize="off">')."\n",($qb?html_select("collation",array(""=>"(".lang(113).")")+$qb,$L["collation"]).'':"")."\n",'<input type=\'submit\' value=\'',lang(18),'\'>
';if(DB!="")echo"<input type='submit' name='drop' value='".lang(138)."'".confirm(lang(193,DB)).">\n";elseif(!$_POST["add"]&&$_GET["db"]=="")echo
icon("plus","add[0]","+",lang(120))."\n";echo
input_token(),'</form>
';}elseif(isset($_GET["foreign"])){$a=$_GET["foreign"];$C=$_GET["name"];$L=$_POST;if($_POST&&!$j&&!$_POST["add"]&&!$_POST["change"]&&!$_POST["change-js"]){if(!$_POST["drop"]){$L["source"]=array_filter($L["source"],'strlen');ksort($L["source"]);$jk=array();foreach($L["source"]as$x=>$X)$jk[$x]=$L["target"][$x];$L["target"]=$jk;}$Bb=object_name("FOREIGN",$a,$L["source"]);if(JUSH=="sqlite")$J=recreate_table($a,$a,array(),array(),array(" $C"=>($L["drop"]?"":" ".format_foreign_key($L,$Bb))));else{$ta="ALTER TABLE ".table($a);$J=($C==""||queries("$ta DROP ".(JUSH=="sql"?"FOREIGN KEY ":"CONSTRAINT ").idf_escape($C)));if(!$L["drop"])$J=queries("$ta ADD".format_foreign_key($L,$Bb));}queries_redirect(ME."table=".url_escape($a),($L["drop"]?lang(210):($C!=""?lang(211):lang(212))),$J);if(!$L["drop"])$j=lang(213);}$Jg=false;if(!$_POST&&$C!=""){$Hd=foreign_keys($a);$L=idx($Hd,$C,array());$Jg=!$L;}page_header(($C!=""?lang(214):lang(150)),$j,array("table"=>$a),h($C!=""?$C:$a),$Jg,'');if($_POST){ksort($L["source"]);if($_POST["change"]||$_POST["change-js"])$L["target"]=array();else$L["source"][]="";}elseif($C!="")$L["source"][]="";else{$L["table"]=$a;$L["source"]=array("");}echo'
<form action="" method="post">
';$wj=array_keys(fields($a));if($L["db"]!="")connection()->select_db($L["db"]);if($L["ns"]!=""){$nh=get_schema();set_schema($L["ns"]);}$vi=array_keys(array_filter(table_status('',true),function(array$T){return!$T["dependent"]&&fk_support($T);}));$jk=array_keys(fields(in_array($L["table"],$vi)?$L["table"]:reset($vi)));$b=on('change','foreignChange');echo"<p><label>".lang(215).": ".html_select("table",$vi,$L["table"],$b)."</label>\n";if(JUSH!="sqlite"){$bc=array();foreach(adminer()->databases()as$h){if(!information_schema($h))$bc[]=$h;}echo"<label>".lang(78).": ".html_select("db",$bc,$L["db"]!=""?$L["db"]:$_GET["db"],$b)."</label>";}echo
input_hidden("change-js"),'<noscript><p><input type=\'submit\' name=\'change\' value=\'',lang(216),'\'></noscript>
<table>
<thead><tr><th id="label-source">',lang(147),'<th id="label-target">',lang(148),'<tbody>
';$w=0;foreach($L["source"]as$x=>$X){echo"<tr>","<td>".html_select("source[".(+$x)."]",array(-1=>"")+$wj,$X,($w==count($L["source"])-1?on('change','foreignAddRow'):""),"label-source"),"<td>".html_select("target[".(+$x)."]",$jk,idx($L["target"],$x),"","label-target");$w++;}echo'</table>
<p>
<label>',lang(115),': ',html_select("on_delete",array(-1=>"")+explode("|",driver()->onActions),$L["on_delete"]),'</label>
<label>',lang(114),': ',html_select("on_update",array(-1=>"")+explode("|",driver()->onActions),$L["on_update"]),'</label>
',(support("deferrable")?html_select("deferrable",array('NOT DEFERRABLE','DEFERRABLE','DEFERRABLE INITIALLY DEFERRED'),$L["deferrable"]):''),'<p>
<input type=\'submit\' value=\'',lang(18),'\'>
<noscript><p><input type=\'submit\' name=\'add\' value=\'',lang(217),'\'></noscript>
';if($C!="")echo'<input type=\'submit\' name=\'drop\' value=\'',lang(138),'\'',confirm(lang(193,$C)),'>
';echo
input_token(),'</form>
';}elseif(isset($_GET["view"])){$a=$_GET["view"];$L=$_POST;$oh="VIEW";if(JUSH=="pgsql"&&$a!=""){$Gj=table_status1($a);$oh=strtoupper($Gj["Engine"]);}if($_POST&&!$j){$C=trim($L["name"]);$Aa=" AS\n$L[select]";$A=ME."table=".url_escape($C);$eg=lang(218);$V=($_POST["materialized"]?"MATERIALIZED VIEW":"VIEW");if(!$_POST["drop"]&&$a==$C&&JUSH!="sqlite"&&$V=="VIEW"&&$oh=="VIEW")query_redirect((JUSH=="mssql"?"ALTER":"CREATE OR REPLACE")." VIEW ".table($C).$Aa,$A,$eg);else{$nk="adminer_".uniqid();drop_create("DROP $oh ".table($a),"CREATE $V ".table($C).$Aa,"DROP $V ".table($C),"CREATE $V ".table($nk).$Aa,"DROP $V ".table($nk),($_POST["drop"]?substr(ME,0,-1):$A),lang(219),$eg,lang(220),$a,$C);}}$Jg=false;if(!$_POST&&$a!=""){$L=view($a);$Jg=!$L["select"];$L["name"]=$a;$L["materialized"]=($oh!="VIEW");if(!$j)$j=adminer()->error();}page_header(($a!=""?lang(43):lang(221)),$j,array("table"=>$a),h($a),$Jg,doc_link(array('sqlite'=>"lang_createview.html",)));echo'
<form action="" method="post">
<p>',lang(203),': <input name="name" value="',h($L["name"]),'" data-maxlength="64" autocapitalize="off">
',(support("materializedview")?" ".checkbox("materialized",1,$L["materialized"],lang(141)):""),'<p>';textarea("select",$L["select"]);echo'<p>
<input type=\'submit\' value=\'',lang(18),'\'>
';if($a!="")echo'<input type=\'submit\' name=\'drop\' value=\'',lang(138),'\'',confirm(lang(193,$a)),'>
';echo
input_token(),'</form>
';}elseif(isset($_GET["check"])){$a=$_GET["check"];$C="$_GET[name]";$L=$_POST;if($L&&!$j){$A=ME."table=".url_escape($a);$hg=lang(222);$fg=lang(223);$gg=lang(224);if(JUSH=="sqlite")queries_redirect($A,($L["drop"]?$hg:($C!=""?$fg:$gg)),recreate_table($a,$a,array(),array(),array(),"",array(),"$C",($L["drop"]?"":$L["clause"])));else{$ta="ALTER TABLE ".table($a);$eb=" CHECK ($L[clause])";$nk="adminer_".uniqid();drop_create("$ta DROP CONSTRAINT ".idf_escape($C),"$ta ADD".($L["name"]!=""?" CONSTRAINT ".idf_escape($L["name"]):"").$eb,"$ta DROP CONSTRAINT ".idf_escape($L["name"]),"$ta ADD CONSTRAINT ".idf_escape($nk).$eb,"$ta DROP CONSTRAINT ".idf_escape($nk),$A,$hg,$fg,$gg,$C,$L["name"]);}}$Jg=false;if(!$L){$hb=driver()->checkConstraints($a);$Jg=($C!=""&&!$hb[$C]);$L=array("name"=>($C!=""?$C:object_name("CHECK",$a,array())),"clause"=>$hb[$C]);}page_header(($C!=""?lang(225):lang(152)),$j,array("table"=>$a),h($C!=""?$C:$a),$Jg,doc_link(array('sqlite'=>"lang_createtable.html#check_constraints",)));echo'
<form action="" method="post">
';if(JUSH!="sqlite")echo'<p>'.lang(203).': <input name="name" value="'.h($L["name"]).'" data-maxlength="64" autocapitalize="off">';echo'<p>';textarea("clause",$L["clause"]);echo'<p><input type=\'submit\' value=\'',lang(18),'\'>
';if($C!="")echo'<input type=\'submit\' name=\'drop\' value=\'',lang(138),'\'',confirm(lang(193,$C)),'>
';echo
input_token(),'</form>
';}elseif(isset($_GET["trigger"])){$a=$_GET["trigger"];$C="$_GET[name]";$Lk=trigger_options();$L=trigger($C,$a);$Jg=($C!=""&&!$L);$zg=str_replace("{table}",$a,adminer()->namePattern("TRIGGER"));$L+=array("Trigger"=>strtr($zg,array("{timing}"=>"b","{event}"=>"i","{columns}"=>"","{type}"=>"row")));if($_POST){if(!$j&&in_array($_POST["Timing"],$Lk["Timing"])&&in_array($_POST["Event"],$Lk["Event"])&&in_array($_POST["Type"],$Lk["Type"])){$Vg=" ON ".table($a);$Cc="DROP TRIGGER ".idf_escape($C).(JUSH=="pgsql"?$Vg:"");$A=ME."table=".url_escape($a);if($_POST["drop"])query_redirect($Cc,$A,lang(226));else{if($C!="")queries($Cc);queries_redirect($A,($C!=""?lang(227):lang(228)),queries(create_trigger($Vg,$_POST)));if($C!="")queries(create_trigger($Vg,$L+array("Type"=>reset($Lk["Type"]))));}}$L=$_POST;}page_header(($C!=""?lang(229):lang(155)),$j,array("table"=>$a),h($C!=""?$C:$a),$Jg,doc_link(array('sqlite'=>"lang_createtrigger.html",)));$Bg=strtr(preg_quote($zg),array('\{timing\}'=>'[abi]','\{event\}'=>'[iud]*','\{columns\}'=>'.*','\{type\}'=>'(row|statement)'));$Jk=on('change','triggerChange',"^$Bg$",$zg);$Og=on('input','triggerChange',"^$Bg$",$zg);echo'
<form action="" method="post" id="form">
<table class="layout">
<tr><th>',lang(230),'<td>',html_select("Timing",$Lk["Timing"],$L["Timing"],$Jk),'<tr><th>',lang(231),'<td>',html_select("Event",$Lk["Event"],$L["Event"],$Jk),(in_array("UPDATE OF",$Lk["Event"])?" <input name='Of' value='".h($L["Of"])."' class='hidden'$Og>":""),'<tr><th>',lang(49),'<td>',html_select("Type",$Lk["Type"],$L["Type"],$Jk),'<tr><th>',lang(203),'<td><input name="Trigger" value="',h($L["Trigger"]),'" data-maxlength="64" autocapitalize="off">
</table>
',script("fire(qs('#form')['Timing'], 'change');"),'<p>';textarea("Statement",$L["Statement"]);echo'<p>
<input type=\'submit\' value=\'',lang(18),'\'>
';if($C!="")echo'<input type=\'submit\' name=\'drop\' value=\'',lang(138),'\'',confirm(lang(193,$C)),'>
';echo
input_token(),'</form>
';}elseif($_GET["select"]!=""){$a=$_GET["select"];$T=table_status1($a);$v=indexes($a);$l=fields($a);$Hd=column_foreign_keys($a);$Tg=$T["Oid"];$Ji=array();$e=array();$Vi=array();$gh=array();$qk=null;foreach($l
as$x=>$k){$C=adminer()->fieldName($k);$Ag=html_entity_decode(strip_tags($C),ENT_QUOTES);if(isset($k["privileges"]["select"])&&$C!=""){$e[$x]=$Ag;if(is_shortable($k))$qk=adminer()->selectLengthProcess();}if(isset($k["privileges"]["where"])&&$C!="")$Vi[$x]=$Ag;if(isset($k["privileges"]["order"])&&$C!="")$gh[$x]=$Ag;$Ji+=$k["privileges"];}list($O,$q)=adminer()->selectColumnsProcess($e,$v);$O=array_unique($O);$q=array_unique($q);$bf=count($q)<count($O);$Z=adminer()->selectSearchProcess($l,$v,$T);$E=adminer()->selectOrderProcess($l,$v);$z=adminer()->selectLimitProcess();if($_GET["val"]&&is_ajax()){header("Content-Type: text/plain; charset=utf-8");foreach($_GET["val"]as$Xk=>$L){$Aa=convert_field($l[key($L)]);$O=array($Aa?:idf_escape(key($L)));$Z[]=where_check(bracket_escape($Xk,true),$l);$K=driver()->select($a,$O,$Z,$O);if($K)echo
first($K->fetch_row());}exit;}$fi=$Zk=array();foreach($v
as$u){if($u["type"]=="PRIMARY"){$fi=array_flip($u["columns"]);$Zk=($O?$fi:array());foreach($Zk
as$x=>$X){if(in_array(idf_escape($x),$O))unset($Zk[$x]);}break;}}if($Tg&&!$fi){$fi=$Zk=array($Tg=>0);$v[]=array("type"=>"PRIMARY","columns"=>array($Tg));}if($_POST&&!$j){$El=$Z;if(!$_POST["all"]&&is_array($_POST["check"])){$hb=array();foreach($_POST["check"]as$eb)$hb[]=where_check($eb,$l);$El[]="((".implode(") OR (",$hb)."))";}$Gl=$El;$El=($El?"\nWHERE ".implode(" AND ",$El):"");if($_POST["export"]){save_settings(array("output"=>$_POST["output"],"format"=>$_POST["format"]),"adminer_import");dump_headers($a);adminer()->dumpTable($a,"");$Yi=($O?:array("*"));$Hb=convert_fields($e,$l,$O);if($Hb)$Yi[]=substr($Hb,2);$I="";if(is_array($_POST["check"])&&!$fi){$Nd=implode(", ",$Yi)."\nFROM ".table($a);$Xd=($q&&$bf?"\nGROUP BY ".implode(", ",$q):"").($E?"\nORDER BY ".implode(", ",$E):"");$Uk=array();foreach($_POST["check"]as$X)$Uk[]="(SELECT".limit($Nd,"\nWHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check($X,$l).$Xd,1).")";$I=implode(" UNION ALL ",$Uk);}adminer()->dumpData($a,"table",$I,$Yi,$Gl,($bf?$q:array()),$E);adminer()->dumpFooter();exit;}if(!adminer()->selectEmailProcess($Z,$Hd)){if($_POST["save"]||$_POST["delete"]){$J=true;$ma=0;$Qa=false;$Q=array();if(!$_POST["delete"]){foreach($l
as$C=>$X){$t=bracket_escape($C);if(isset($_POST["fields"][$t])||$_FILES["fields-$t"]){$X=process_input($l[$C]);if($X!==null&&($_POST["clone"]||$X!==false))$Q[idf_escape($C)]=($X!==false?$X:idf_escape($C));}}}if($_POST["delete"]||$Q){$I=($_POST["clone"]?"INTO ".table($a)." (".implode(", ",array_keys($Q)).")\nSELECT ".implode(", ",$Q)."\nFROM ".table($a):"");if($_POST["all"]||($fi&&is_array($_POST["check"]))||$bf){$J=($_POST["delete"]?driver()->delete($a,$El):($_POST["clone"]?queries("INSERT $I$El".driver()->insertReturning($a)):driver()->update($a,$Q,$El)));$ma=connection()->affected_rows;if(is_object($J))$ma+=$J->num_rows;}else{$Qa=count((array)$_POST["check"])>1&&driver()->begin();foreach((array)$_POST["check"]as$X){$Dl="\nWHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check($X,$l);$J=($_POST["delete"]?driver()->delete($a,$Dl,1):($_POST["clone"]?queries("INSERT".limit1($a,$I,$Dl)):driver()->update($a,$Q,$Dl,1)));if(!$J)break;$ma+=connection()->affected_rows;}if($Qa&&$J&&!driver()->commit())$J=false;}}$eg=lang(164,$ma);if($_POST["clone"]&&$J&&$ma==1){$uf=last_id($J);if($uf)$eg=lang(186," $uf");}queries_redirect(remove_from_uri($_POST["all"]&&$_POST["delete"]?"page|next":""),$eg,$J);if($Qa)driver()->rollback();if(!$_POST["delete"]){$Xh=(array)$_POST["fields"];edit_form($a,array_intersect_key($l,$Xh),$Xh,!$_POST["clone"],$j);page_footer();exit;}}elseif(!$_POST["import"]){$J=true;$ma=0;$Qa=count((array)$_POST["val"])>1&&driver()->begin();foreach((array)$_POST["val"]as$Xk=>$L){$Q=array();foreach($L
as$x=>$X){$x=bracket_escape($x,true);$Q[idf_escape($x)]=(preg_match('~char|text~',$l[$x]["type"])||$X!=""?adminer()->processInput($l[$x],$X):"NULL");}$J=driver()->update($a,$Q," WHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check(bracket_escape($Xk,true),$l),($bf||$fi?0:1)," ");if(!$J)break;$ma+=connection()->affected_rows;}if($Qa)$J=$J&&driver()->commit();queries_redirect(remove_from_uri(),lang(164,$ma),$J);if($Qa)driver()->rollback();}else{save_settings(array("format"=>$_POST["separator"]),"adminer_import");$vd=get_file("csv_file",true);if(!is_string($vd))$j=upload_error($vd);elseif(!preg_match('~~u',$vd))$j=lang(232);else{$rb=array_keys($l);$dj=($_POST["separator"]=="csv"?",":($_POST["separator"]=="tsv"?"\t":";"));$Qb=parse_csv($vd,$dj);$ma=count($Qb);driver()->begin();$M=array();foreach($Qb
as$x=>$ul){if(!$x&&!array_diff($ul,$rb)){$rb=$ul;$ma--;}else{$Q=array();foreach($ul
as$r=>$c)$Q[idf_escape($rb[$r])]=($c==""&&$l[$rb[$r]]["null"]?"NULL":q(csv_value($c)));$M[]=$Q;}}$J=(!$M||driver()->insertUpdate($a,$M,$fi));if($J)driver()->commit();queries_redirect(remove_from_uri("page|next"),lang(233,$ma),$J);driver()->rollback();}}}}$Yj=adminer()->tableName($T);if(is_ajax()){page_headers();ob_start();}else
page_header(lang(53).": $Yj",$j,array(),"",(!$l&&support("table")),($l?doc_link(array(JUSH=>driver()->tableHelp($a,is_view($T)))):""));$Q=null;if(isset($Ji["insert"])||!support("table")){$Q="";foreach((array)$_GET["where"]as$X){$Y=$X["val"];if(is_array($Y))$Y=(count($Y)==1&&preg_match('~^val-(.*)~s',reset($Y),$B)?$B[1]:"");if($X["col"]!=""&&$Y!=""&&($X["op"]=="="||(!$X["op"]&&(is_array($X["val"])||!preg_match('~[_%]~',$Y)))))$Q
.="&set[".url_escape(bracket_escape($X["col"]))."]=".url_escape($Y);}}adminer()->selectLinks($T,$Q);if(!$e&&support("table"))echo"<p class='error'>".lang(234)."\n";else{echo"<form action='' id='form'>\n","<div hidden>";hidden_fields_get();echo(DB!=""?input_hidden("db",DB).(isset($_GET["ns"])?input_hidden("ns",$_GET["ns"]):""):""),input_hidden("select",$a),"</div>\n";adminer()->selectColumnsPrint($O,$e);adminer()->selectSearchPrint($Z,$Vi,$v,$T);adminer()->selectOrderPrint($E,$gh,$v);adminer()->selectLimitPrint($z);if($qk!==null)adminer()->selectLengthPrint($qk);adminer()->selectActionPrint($v);echo"</form>\n";foreach((array)$_GET["where"]as$X){if($X["op"]=="SQL"&&!in_array($_SERVER["HTTP_SEC_FETCH_SITE"],array("","same-origin"))){echo"<p class='error'>".lang(108).' '.lang(109)."\n";page_footer();exit;}}$F=$_GET["page"];$Kd=null;if($F=="last"){$Kd=get_val(count_rows($a,$Z,$bf,$q));$F=floor(max(0,intval($Kd)-1)/$z);}$Xi=$O;$Wd=$q;if(!$Xi){$Xi[]="*";$Hb=convert_fields($e,$l,$O);if($Hb)$Xi[]=substr($Hb,2);}foreach($O
as$x=>$X){$k=$l[idf_unescape($X)];if($k&&($Aa=convert_field($k)))$Xi[$x]="$Aa AS $X";}if(JUSH=="pgsql"||JUSH=="mssql"){foreach((array)$_GET["columns"]as$x=>$X){if(isset($Xi[$x])&&$X["fun"])$Xi[$x].=" AS ".idf_escape(apply_sql_function($X["fun"],($X["col"]!=""?$X["col"]:"*")));}}if(!$bf&&$Zk){foreach($Zk
as$x=>$X){$Xi[]=idf_escape($x);if($Wd)$Wd[]=idf_escape($x);}}$J=driver()->select($a,$Xi,$Z,$Wd,$E,$z,$F,true);if(!is_object($J))echo"<p class='error'>".(adminer()->error()?:lang(26))."\n";else{if(JUSH=="mssql"&&$F)$J->seek($z*$F);$Pc=array();$M=array();while($L=$J->fetch_assoc()){if($F&&JUSH=="oracle")unset($L["RNUM"]);$M[]=$L;}$he=($z&&(support("cursor")?$_GET["next"]!="":count($M)>=$z));if(is_ajax()&&$he)header("X-Next-Page: ".pagination_href($F+1));if($_GET["modify"]&&$M){$Vf=max_input_vars(count($M[0])+1,20);echo($Vf&&count($M)>$Vf?"<p class='error'>".max_input_vars_error()."\n":"");}echo"<form action='' method='post' enctype='multipart/form-data'".on_upload_progress($fl).">\n";if($_GET["page"]!="last"&&$z&&$q&&$bf&&JUSH=="sql")$Kd=get_val(" SELECT FOUND_ROWS()");if(!$M)echo"<p class='message'>".lang(16)."\n";else{$Ma=adminer()->backwardKeys($a,$Yj);$Hi=array();reset($O);foreach($M[0]as$x=>$X){if(!isset($Zk[$x])){$X=idx($_GET["columns"],key($O))?:array();$Hi[$x]=array("fun"=>$X["fun"],"col"=>($O?$X["col"]:$x));next($O);}}echo"<div class='scrollable'>","<table id='table' class='nowrap checkable odds'".on('click','tableClick').on('dblclick','tableClick').on('keydown','editingKeydown').">\n","<thead><tr>".(!$q&&$O?"":"<td class='hover check sticky'><input type='checkbox' id='all-page' class='jsonly' title='".lang(235)."'".on('click','formCheck','^check').">");$Cg=array();$si=1;foreach($Hi
as$x=>$X){$k=$l[$X["col"]];$C=($k?adminer()->fieldName($k,$si):($X["fun"]?"*":h($x)));if($C!=""){$si++;$Cg[$x]=$C;$d=idf_escape($x);$we=remove_from_uri('(order|desc)[^=]*|page|next').'&order[0]='.url_escape($x);$jc="&desc[0]=1";$tj=preg_replace('~ DESC( NULLS LAST)?$~','',$E[0]);$vj=($tj==$d||$tj==$x);echo"<th id='th[".h(bracket_escape($x))."]'".($vj?" aria-sort='".($tj==$E[0]?"ascending":"descending")."'":"").">";$Rd=apply_sql_function(h($X["fun"]),$C);$uj=isset($k["privileges"]["order"])||$X["fun"];echo($uj?"<a href='".h($we.($vj&&$tj==$E[0]?$jc:''))."'>$Rd</a>":$Rd);$dg=($uj?"<a href='".h($we.$jc)."' title='".lang(59)."' class='text'> ↓</a>":'');if(!$X["fun"]&&isset($k["privileges"]["where"]))$dg
.="<a href='#fieldset-search' title='".lang(56)."' class='text jsonly'".on('click','selectSearch',$x)."> =</a>";echo($dg?"<span class='column'>$dg</span>":"");}}$Af=array();if($_GET["modify"]){foreach($M
as$L){foreach($L
as$x=>$X)$Af[$x]=max($Af[$x],min(40,utf8_length((string)$X)));}}$re=array();$qe=array();foreach((array)$_GET["where"]as$X){$X+=array("col"=>"","op"=>"","val"=>"");$c=$X["col"];$Ui=$X["val"];if(!is_array($Ui)&&($Ui!=""||preg_match('~NULL$~',$X["op"]))&&(!$X["op"]||in_array($X["op"],adminer()->operators($T)))){$Cf=strtr(preg_quote($Ui),array("%"=>".*?","_"=>"."));$Nh=array("LIKE %%"=>$Cf,"ILIKE %%"=>$Cf,"REGEXP"=>$Ui)+(JUSH=="pgsql"?array("~"=>$Ui,"~*"=>$Ui):array())+($c!=""?array():array("="=>'^'.preg_quote($Ui).'\z',"IN"=>'^(?:'.implode("|",array_map('preg_quote',array_map('trim',explode(",",$Ui)))).')\z',"LIKE"=>"^$Cf\\z","ILIKE"=>"^$Cf\\z","FIND_IN_SET"=>'(?<=^|,)'.preg_quote($Ui).'(?=,|\z)',));foreach(($c!=""?array($c=>$l[$c]):$l)as$C=>$k){if($c!=""||is_searchable($k,$X)){$Xg=$X["op"]?:(!preg_match('~'.text_type().'~',$k["type"])?"IN":(preg_match('~%~',$Ui)?"LIKE":"LIKE %%"));if(isset($Nh[$Xg])){$kb=preg_match('~^ILIKE|\*$~',$Xg)||($Xg!="~"&&preg_match('~^(sql|mssql|sqlite)$~',JUSH));$re[$C][]="(?".($kb?"i":"").":$Nh[$Xg])";}elseif($Xg=="IS NULL"&&$c=="")$qe[$C]=true;}}}}echo($Ma?"<th>".lang(236):"")."<tbody>\n";if(is_ajax())ob_end_clean();foreach(adminer()->rowDescriptions($M,$Hd)as$xg=>$L){$Wk=unique_array($M[$xg],$v);if(!$Wk){$Wk=array();foreach($M[$xg]as$x=>$X){if(!in_array(idx(idx($Hi,$x,array()),"fun"),driver()->grouping))$Wk[$x]=$X;}}$Xk="";$r=0;foreach($Wk
as$x=>$X){$Gi=idx($Hi,$x,array());$Rd=idx($Gi,"fun","");$c=($Rd?$Gi["col"]:$x);$k=(array)$l[$c];$af=is_blob($k);if(!$Rd&&strlen($X)>64&&driver()->md5(idf_escape($c),$k)){$Rd="md5";$X=md5($af?(string)driver()->value($X,$k):$X);}if($Rd){$Xk
.="&fun[$r]=".url_escape($Rd)."&col[$r]=".url_escape($c).($X!==null?"&val[$r]=".url_escape($X===false?"f":$X):"");$r++;}else$Xk
.="&".($X!==null?"where[".url_escape(bracket_escape($c))."]=".url_escape($X===false?"f":$X):"null[]=".url_escape($c));}echo"<tr>".(!$q&&$O?"":"<td class='hover check sticky'>".($bf||information_schema(DB)?"":"<a href='".h(ME."edit=".url_escape($a).$Xk)."' class='edit'>".lang(237)."</a> ").checkbox("check[]",substr($Xk,1),in_array(substr($Xk,1),(array)$_POST["check"])));foreach($L
as$x=>$X){if(isset($Cg[$x])){$Rd=$Hi[$x]["fun"];$c=$Hi[$x]["col"];$k=(array)$l[$x];if($X!=""&&(!isset($Pc[$x])||$Pc[$x]!=""))$Pc[$x]=(is_mail($X)?$Cg[$x]:"");$_="";if(is_blob($k)&&$X!="")$_=ME.'download='.url_escape($a).'&field='.url_escape($x).$Xk;if(!$_&&$X!==null){foreach((array)$Hd[$x]as$n){if(count($Hd[$x])==1||end($n["source"])==$x){$_="";foreach($n["source"]as$r=>$wj)$_
.=where_link($r,$n["target"][$r],$M[$xg][$wj]);$_=($n["db"]!=""?preg_replace('~([?&]db=)[^&]+~','\1'.url_escape($n["db"]),ME):ME).'select='.url_escape($n["table"]).$_;if($n["ns"])$_=preg_replace('~([?&]ns=)[^&]+~','\1'.url_escape($n["ns"]),$_);if(count($n["source"])==1)break;}}}if($Rd=="count"&&$c==""){$_=ME."select=".url_escape($a);$r=0;foreach((array)$_GET["where"]as$W){if(!array_key_exists($W["col"],$Wk))$_
.=where_link($r++,$W["col"],$W["val"],$W["op"]);}foreach($Wk
as$if=>$W){if(idx(idx($Hi,$if,array()),"fun")){$_="";break;}$_
.=where_link($r++,$if,$W);}}$xe=select_value($X,$_,$k,$qk,($Rd?array():idx($re,$x,array())));if($X===null&&!$Rd&&isset($qe[$x]))$xe="<mark>$xe</mark>";$t=bracket_escape($Xk);$s=h("val[$t][".bracket_escape($x)."]");$Zh=idx(idx($_POST["val"],$t),bracket_escape($x));$cl=idx($k["privileges"],"update")&&!is_identity_always($k);$Lc=!is_array($L[$x])&&!is_blob($k)&&is_utf8($X)&&$M[$xg][$x]==$X&&!$Rd&&!$k["generated"]&&$cl;$V=($Rd=="min"||$Rd=="max"?$l[$c]["type"]:$k["type"]);$pk=preg_match('~text|json|lob~',$V);$cf=preg_match(number_type(),$V)||preg_match('~^(avg|ceil|char_length|count|count distinct|floor|len|length|round|sum|time_to_sec)$~',$Rd);echo"<td id='$s'".($cf&&($X===null||is_numeric(strip_tags($xe))||$V=="money")?" class='number'":"");if(($_GET["modify"]&&$Lc&&$X!==null)||$Zh!==null){$ce=h($Zh!==null?$Zh:$X);echo">".($pk?"<textarea name='$s' cols='30' rows='".(substr_count($X,"\n")+1)."'>$ce</textarea>":"<input name='$s' value='$ce' size='$Af[$x]'>");}else{$Kf=strpos($xe,"<i>…</i>");echo($cl?" data-text='".($Kf?2:($pk?1:0))."'".($Lc?"":" data-warning='".lang(238)."'"):"").">$xe";}}}if($Ma)echo"<td>";adminer()->backwardKeysPrint($Ma,$M[$xg]);echo"</tr>\n";}if(is_ajax())exit;echo"</table>\n","</div>\n";}if(!is_ajax()){$la=get_settings("adminer_import");if($M||$F||$he){$dd=true;if($_GET["page"]!="last"){if(!$z||(count($M)<$z&&($M||!$F)))$Kd=($F?$F*$z:0)+count($M);elseif(JUSH!="sql"||!$bf){$Kd=($bf?null:found_rows($T,$Z));$dd=!driver()->hasEstimatedRows();if($Kd===null||(!$dd&&$Kd<max(1e4,2*($F+1)*$z))){$Kd=first(slow_query(count_rows($a,$Z,$bf,$q)));$dd=true;}}}if(!support("cursor"))$he=(($Kd===false?count($M)+1:$Kd-$F*$z)>$z);$wh=($z&&($he||$F));if($wh)echo($he?'<p><a href="'.h(pagination_href($F+1)).'" class="loadmore"'.on('click','selectLoadMore',lang(239)).'>'.lang(240).'</a>':''),"\n";echo"<div class='footer'><div>\n";if($wh){$Tf=($Kd===false?$F+($M?(count($M)>=$z?2:1):0):floor(($Kd-1)/$z));echo"<fieldset><legend>".lang(241)."</legend>";if(!support("cursor")){echo
pagination(0,$F).($F>5?" …":"");for($r=max(1,$F-4);$r<min($Tf,$F+5);$r++)echo
pagination($r,$F);if($Tf>0)echo($F+5<$Tf?" …":""),($dd&&$Kd!==false?pagination($Tf,$F):" <a href='".h(remove_from_uri("page")."&page=last")."' title='~$Tf'>".lang(242)."</a>");}else
echo
pagination(0,$F).($F>1?" …":""),($F?pagination($F,$F):""),($he?pagination($F+1,$F)." …":"");echo"</fieldset>\n";}echo"<fieldset>","<legend>".lang(243)."</legend>";$rc=($dd?"":"~ ").$Kd;$nf=($Kd!==false?($dd?"":"~ ").lang(168,$Kd):"");echo
checkbox("all",1,0,$nf,on('click','countRows',$rc))."\n","</fieldset>\n";if(adminer()->selectCommandPrint())echo'<fieldset',($_GET["modify"]?'':" title='".lang(169)."'"),'>
<legend><a href=\'',h($_GET["modify"]?remove_from_uri("modify"):relative_uri()."&modify=1"),'\'>',lang(244),'</a></legend><div>
<input type=\'submit\' id=\'save\' value=\'',lang(18),'\'',($_GET["modify"]||$_POST["val"]?'':" class='jsonly' disabled"),'>
</div></fieldset>

<fieldset><legend>',lang(137),' <span id="selected"></span></legend><div>
<input type=\'submit\' name=\'edit\' value=\'',lang(14),'\'>
<input type=\'submit\' name=\'clone\' value=\'',lang(245),'\'>
<input type=\'submit\' name=\'delete\' value=\'',lang(22),'\'',confirm(),'>
</div></fieldset>
';$Id=adminer()->dumpFormat();foreach((array)$_GET["columns"]as$d){if($d["fun"]){unset($Id['sql']);break;}}if($Id){print_fieldset("export",lang(76)." <span id='selected2'></span>");$th=adminer()->dumpOutput();echo($th?html_select("output",$th,$la["output"])." ":""),html_select("format",$Id,$la["format"])," <input type='submit' name='export' value='".lang(76)."'>\n","</div></fieldset>\n";}adminer()->selectEmailPrint(array_filter($Pc,'strlen'),$e);echo"</div></div>\n";}if(adminer()->selectImportPrint())echo"<p>","<a href='#import' class='toggle'>".lang(75)."</a>","<span id='import'".($_POST["import"]?"":" class='hidden'").">: ",($fl?input_hidden(ini_get("session.upload_progress.name"),$fl):""),file_input(" name='csv_file'"," ".html_select("separator",array("csv"=>"CSV,","csv;"=>"CSV;","tsv"=>"TSV"),$la["format"])." <input type='submit' name='import' value='".lang(75)."'>".($fl?" <progress class='jsonly hidden' max='1' value='0'></progress>":"")),"</span>";echo
input_token(),"</form>\n",(!$q&&$O?"":script("tableCheck();"));}}}if(is_ajax()){ob_end_clean();exit;}}elseif(isset($_GET["variables"])){$Gj=isset($_GET["status"]);page_header($Gj?lang(129):lang(128));$vl=($Gj?adminer()->showStatus():adminer()->showVariables());if(!$vl)echo"<p class='message'>".lang(16)."\n";else{echo"<table>\n";foreach($vl
as$L){echo"<tr>";$x=array_shift($L);echo"<th><code class='jush-".JUSH.($Gj?"status":"set")."'>".h($x)."</code>";foreach($L
as$X)echo"<td>".nl_br(h($X));}echo"</table>\n";}}elseif(isset($_GET["script"])){header("Content-Type: application/json; charset=utf-8");if($_GET["script"]=="db"){$Pj=array("Data_length"=>0,"Index_length"=>0,"Data_free"=>0);foreach(table_status()as$C=>$T){json_row("Comment-$C",h($T["Comment"]).($T["Error"]?" <span class='error'>".h($T["Error"])."</span>":""));if(!is_view($T)||preg_match('~materialized~i',$T["Engine"])){foreach(array("Engine","Collation")as$x)json_row("$x-$C",h($T[$x]));foreach(array_keys($Pj+array("Auto_increment"=>0,"Rows"=>0))as$x){if(array_key_exists($x,$T))json_row("$x-$C",format_status($T,$x));if($T[$x]!=""&&isset($Pj[$x]))$Pj[$x]+=($T["Engine"]!="InnoDB"||$x!="Data_free"?$T[$x]:0);}}}if(function_exists('Adminer\db_status'))$Pj=db_status();foreach($Pj
as$x=>$X)json_row("sum-$x",format_number($X));json_row("");}elseif($_GET["script"]=="kill"){if(!$j)connection()->query("KILL ".number($_POST["kill"]));}else{foreach(count_tables(adminer()->databases(false))as$h=>$X){json_row("tables-$h",format_number($X));json_row("size-$h",db_size($h));}json_row("");}exit;}else{if(!isset($_GET["select"])&&support("single_table")){$U=tables_list();if($U)redirect(ME.(support("table")?"table=":"select=").url_escape(key($U)));}$ag=ME.(isset($_GET["select"])?"select=&":"");$hk=array_merge((array)$_POST["tables"],(array)$_POST["views"]);if($hk&&!$j&&!$_POST["search"]){$J=true;$eg="";if(JUSH=="sql"&&$_POST["tables"]&&count($_POST["tables"])>1&&($_POST["drop"]||$_POST["truncate"]||$_POST["copy"]))queries("SET foreign_key_checks = 0");if($_POST["truncate"]){if($_POST["tables"])$J=truncate_tables($_POST["tables"]);$eg=lang(246);}elseif($_POST["move"]){$J=move_tables((array)$_POST["tables"],(array)$_POST["views"],$_POST["target"]);$eg=lang(247);}elseif($_POST["copy"]){$J=copy_tables((array)$_POST["tables"],(array)$_POST["views"],$_POST["target"]);$eg=lang(248);}elseif($_POST["drop"]){if($_POST["views"])$J=drop_views($_POST["views"]);if($J&&$_POST["tables"])$J=drop_tables($_POST["tables"]);$eg=lang(249);}elseif(JUSH=="sqlite"&&$_POST["check"]){foreach((array)$_POST["tables"]as$S){foreach(get_rows("PRAGMA integrity_check(".q($S).")")as$L)$eg
.="<b>".h($S)."</b>: ".h($L["integrity_check"])."<br>";}}elseif(JUSH=="mssql"&&$_POST["check"]){foreach((array)$_POST["tables"]as$S){foreach(get_rows("DBCC CHECKTABLE (".q(table($S)).") WITH TABLERESULTS")as$L)$eg
.="<b>".h($S)."</b>: ".h($L["MessageText"])."<br>";}}elseif(JUSH!="sql"){$J=(JUSH=="sqlite"?queries("VACUUM"):apply_queries("VACUUM".($_POST["optimize"]?" ANALYZE":""),(array)$_POST["tables"]));$eg=lang(250);}elseif(!$_POST["tables"])$eg=lang(13);elseif($J=queries(($_POST["optimize"]?"OPTIMIZE":($_POST["check"]?"CHECK":($_POST["repair"]?"REPAIR":"ANALYZE")))." TABLE ".implode(", ",array_map('Adminer\idf_escape',$_POST["tables"])))){while($L=$J->fetch_assoc())$eg
.="<b>".h($L["Table"])."</b>: ".h($L["Msg_text"])."<br>";}queries_redirect(relative_uri(),$eg,$J);}page_header(($_GET["ns"]==""?lang(35).": ".h(DB):lang(161).": ".h($_GET["ns"])),$j,true);if(adminer()->homepage()){if($_GET["ns"]!==""){$E=$_GET["order"];$Pd=($E||support("fast_status"));echo"<div>\n","<h3 id='tables-views'>".lang(251)."</h3>\n";$gk=($Pd?table_status():tables_list());if(!$gk)echo"<p class='message'>".lang(13)."\n";else{echo"<form action='' method='post'>\n";if(support("table")){echo"<fieldset><legend>".lang(252)." <span id='selected2'></span></legend><div>",html_select("op",adminer()->operators(),idx($_POST,"op",JUSH=="elastic"?"should":"LIKE %%"))," <input type='search' name='query' value='".h($_POST["query"])."'".on('keydown','submitKeydown','search').">"," <input type='submit' name='search' value='".lang(56)."'>\n","</div></fieldset>\n";if(!$j&&$_POST["search"]&&$_POST["query"]!=""){$_GET["where"][0]["op"]=$_POST["op"];search_tables();}}echo"<div class='scrollable'>\n","<table class='nowrap checkable odds'".on('click','tableClick').on('dblclick','tableClick').">\n",'<thead><tr>','<td class="hover"><input id="check-all" type="checkbox" class="jsonly" title="'.lang(163).'"'.on('click','formCheck','^(tables|views)\[').'>','<th class="sticky"'.(!$E&&JUSH!='sqlite'?" aria-sort='ascending'":'').'><a href="'.h(substr($ag,0,-1)).'">'.lang(143).'</a>';$e=array("Engine"=>array(lang(253).''));if(collations())$e["Collation"]=array(lang(133).'');if(function_exists('Adminer\alter_table'))$e["Data_length"]=array(lang(254).'',"create",lang(44),);if(support("indexes"))$e["Index_length"]=array(lang(255).'',"indexes",lang(146),);$e["Data_free"]=array(lang(256).'',"edit",lang(45));if(function_exists('Adminer\alter_table'))$e["Auto_increment"]=array(lang(51).'',"auto_increment=1&create",lang(44),);$e["Rows"]=array(lang(257).'',"select",lang(41),);if(support("comment"))$e["Comment"]=array(lang(50).'',);$Ba=array('Engine','Collation','Comment');foreach($e
as$x=>$d)echo"<th".($E==$x?" aria-sort='".(in_array($x,$Ba)?"ascending":"descending")."'":"")."><a href='".h($ag)."order=$x'>$d[0]</a>";echo"<tbody>\n";if($E){uasort($gk,function($da,$Ja)use($E,$Ba){$K=($da[$E]<$Ja[$E]?-1:($da[$E]>$Ja[$E]?1:0));return(in_array($E,$Ba)?$K:-$K);});}$U=0;$Pj=array("Data_length"=>0,"Index_length"=>0,"Data_free"=>0);foreach($gk
as$C=>$Gj){$yl=($Pd?is_view($Gj):$Gj!==null&&!preg_match('~table|sequence~i',$Gj));$Gj=($Pd?$Gj:array('Engine'=>$Gj));$s=h("Table-".$C);echo'<tr><td class="hover">'.checkbox(($yl?"views[]":"tables[]"),$C,in_array("$C",$hk,true),"","","",$s),'<th class="sticky">'.(support("table")||support("indexes")?"<a href='".h(ME)."table=".url_escape($C)."' title='".lang(42)."' id='$s'>".h($C).'</a>':h($C));if($yl&&!preg_match('~materialized~i',$Gj['Engine'])){$vk=lang(142);echo'<td colspan="'.(count($e)-(support("comment")?2:1)).'">'.(support("view")?"<a href='".h(ME)."view=".url_escape($C)."' title='".lang(43)."'>$vk</a>":$vk),"<td align='right'><a href='".h(ME)."select=".url_escape($C)."' title='".lang(41)."'>?</a>";if(support("comment"))echo'<td>'.h($Gj['Comment']);}else{if($Pd){foreach(array_keys($Pj)as$x)$Pj[$x]+=($Gj["Engine"]!="InnoDB"||$x!="Data_free"?idx($Gj,$x):0);}foreach($e
as$x=>$d){$s=" id='$x-".h($C)."'";echo($d[1]?"<td align='right'><a href='".h(ME."$d[1]=").url_escape($C)."'$s title='$d[2]'>".format_status($Gj,$x)."</a>":"<td$s>".h(idx($Gj,$x,'?')).($x=="Comment"&&$Gj["Error"]?" <span class='error'>".h($Gj["Error"])."</span>":""));}$U++;}echo"\n";}echo"<tr><td class='hover'><th class='sticky'>".lang(258,count($gk)),"<td>".h(JUSH=="sql"?get_val("SELECT @@default_storage_engine"):""),(collations()?"<td>".h(db_collation(DB,collations())):'');if($Pd&&function_exists('Adminer\db_status'))$Pj=db_status();foreach($Pj
as$x=>$Oj)echo($e[$x]?"<td align='right' id='sum-$x'>".($Pd?format_number($Oj):""):"");echo"\n","</table>\n",($Pd?'':script("ajaxSetHtml('".js_escape(ME)."script=db');")),"</div>\n";if(!information_schema(DB)){$rl="<input type='submit' value='".lang(259)."'".on_help("VACUUM")."> ";$ch="<input type='submit' name='optimize' value='".lang(260)."'".on_help(JUSH=="sql"?"OPTIMIZE TABLE":"VACUUM ANALYZE")."> ";$hi=(JUSH=="sqlite"?$rl."<input type='submit' name='check' value='".lang(261)."'".on_help("PRAGMA integrity_check")."> ":(JUSH=="pgsql"?$rl.$ch:(JUSH=="mssql"?"<input type='submit' name='check' value='".lang(261)."'".on_help("DBCC CHECKTABLE")."> ":(JUSH=="sql"?"<input type='submit' value='".lang(262)."'".on_help("ANALYZE TABLE")."> ".$ch."<input type='submit' name='check' value='".lang(261)."'".on_help("CHECK TABLE")."> "."<input type='submit' name='repair' value='".lang(263)."'".on_help("REPAIR TABLE")."> ":"")))).(function_exists('Adminer\truncate_tables')?"<input type='submit' name='truncate' value='".lang(264)."'".confirm().on_help(JUSH=="sqlite"?"DELETE":"TRUNCATE".(JUSH=="pgsql"?"":" TABLE"))."> ":"").(function_exists('Adminer\drop_tables')?"<input type='submit' name='drop' value='".lang(138)."'".confirm().on_help("DROP TABLE").">":"");echo($hi?"<div class='footer'><div>\n<fieldset><legend>".lang(137)." <span id='selected'></span></legend><div>$hi\n</div></fieldset>\n":"");$g=(support("scheme")?adminer()->schemas():adminer()->databases());if(count($g)!=1&&function_exists('Adminer\move_tables')){echo"<fieldset><legend>".lang(265)." <span id='selected3'></span></legend><div>";$h=(isset($_POST["target"])?$_POST["target"]:(support("scheme")?$_GET["ns"]:DB));echo($g?html_select("target",$g,$h):'<input name="target" value="'.h($h).'" autocapitalize="off">'),"</label> <input type='submit' name='move' value='".lang(121)."'>",(support("copy")?" <input type='submit' name='copy' value='".lang(23)."'> ".checkbox("overwrite",1,$_POST["overwrite"],lang(266)):""),"</div></fieldset>\n";}echo"<input type='hidden' name='all' value=''".on('click','countTables',$U).">\n",input_token(),"</div></div>\n";}echo"</form>\n",script("tableCheck();");}echo(function_exists('Adminer\alter_table')?"<p class='links hover'><a href='".h(ME)."create='>".lang(77)."</a>\n":''),(support("view")?"<a href='".h(ME)."view='>".lang(221)."</a>\n":""),"</div>\n";}}}page_footer();