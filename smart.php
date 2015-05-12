<?php

/**
* php ymall new:project xxxxx
* php ymall (adimn,touch,www) scaffold xxxxx
* php ymall (adimn,touch,www) create:controller xxxxx ccccc ddddd ffffff 
* php ymall create:dao xxxxx
* php ymall create:service xxxxx
*
**/
define("BASE_PATH",dirname(__FILE__).DIRECTORY_SEPARATOR);

define("INIT_NAME","init.php");
define("PROJECT_LIST","admin,touch,www");
define("APP_PATH_NAME","app");
define("CONTROLLER_NAME", "controller");
define("DAO_NAME","dao");
define("SERVICE_NAME","service");
define("CACHE_ROOT_NAME","tmp");
define("DAO_PATH",BASE_PATH.APP_PATH_NAME.DIRECTORY_SEPARATOR.DAO_NAME);
define("SERVICE_PATH",BASE_PATH.APP_PATH_NAME.DIRECTORY_SEPARATOR.SERVICE_NAME);

function run(){ 
	$argv=$_SERVER['argv'];
	makeCommand($argv);
}

function makeCommand($argv){ 
	$project=explode(",",PROJECT_LIST);
	switch($argv){ 
		case $argv[1]=="new:project":
			newProject($argv[2]);
		    break;
		case in_array($argv[1],$project) && ($argv[2]=="add:controller" || $argv[2]=="add:c") && preg_match("/[a-zA-Z0-9]/",$argv[3]) && isset($argv[2]) && isset($argv[3]):
			$project_name=$argv[1];
			$sname=$argv[3];
			unset($argv[0]);
			unset($argv[1]);
			unset($argv[2]);
			unset($argv[3]);
			$func_array=array_values($argv);
			$count=count($func_array);
			if($count>0){ 
				createController($project_name,$sname,$func_array);
			}else{ 
				createController($project_name,$sname);
			}
		    break;
		case in_array($argv[1],$project) && ($argv[2]=="update:controller" || $argv[2]=="update:c") && preg_match("/[a-zA-Z0-9]/",$argv[3]) && isset($argv[2]) && isset($argv[3]):
			$project_name=$argv[1];
			$sname=$argv[3];
			unset($argv[0]);
			unset($argv[1]);
			unset($argv[2]);
			unset($argv[3]);
			$func_array=array_values($argv);
			updateController($project_name,$sname,$func_array);
		    break;    
		case in_array($argv[1],$project) && isset($argv[2]) && preg_match("/scaffold:[a-zA-Z0-9]/",$argv[2]):
		    scaffold($argv);
		    break;   
		case ($argv[1]=="add:dao" || $argv[1]=="add:d") && isset($argv[2]) && preg_match("/[a-zA-Z0-9]/",$argv[2]):
		    createDao($argv[2]);
		    break;
		case ($argv[1]=="add:srv" || $argv[1]=="add:s") && isset($argv[2]) && preg_match("/[a-zA-Z0-9]/",$argv[2]):
		    createService($argv[2]);
		    break; 
		case in_array($argv[1],$project) && isset($argv[2]) && preg_match("/cache:clear/",$argv[2]):
		    cache($argv,'clear');
		    break;     
		case ($argv[1]=="help" || $argv[1]=="-h") && isset($argv[1]) && preg_match("/[-a-zA-Z]/",$argv[1]):
		    help();
		    break;    
		default :
			other_error($argv);
			break;     
	}
}

function newProject($name=""){ 
	if($name!==""){
		$msg_array = array();
		if(file_exists(BASE_PATH.$name)){ 
			//echo "has";
			_error("The project {$name} already exists !");
		}else{ 
			//echo "no has";
			if(isroot()){ 
				$is_create_project=mkdir($name,0777);
				if($is_create_project){ 
					$msg_array[]          = "new project : {$name}";
					$is_create_controller = mkdir($name.DIRECTORY_SEPARATOR.CONTROLLER_NAME,0777);
					$msg_array[]          = $is_create_controller?"new folder : {$name}".DIRECTORY_SEPARATOR.CONTROLLER_NAME:"The controller creation failed , Please check your permissions !";
					$is_create_document   = mkdir($name.DIRECTORY_SEPARATOR."DocumentRoot",0777);	
					$msg_array[]          = $is_create_document?"new folder : {$name}".DIRECTORY_SEPARATOR."DocumentRoot":"The DocumentRoot creation failed , Please check your permissions !";									
					$is_create_template   = mkdir($name.DIRECTORY_SEPARATOR."template",0777);
					$msg_array[]          = $is_create_document?"new folder : {$name}".DIRECTORY_SEPARATOR."template":"The template creation failed , Please check your permissions !";

					if($is_create_document){ 
						$is_create_css  = mkdir($name.DIRECTORY_SEPARATOR."DocumentRoot".DIRECTORY_SEPARATOR."css",0777);
						$msg_array[]    = $is_create_css?"new folder : {$name}".DIRECTORY_SEPARATOR."DocumentRoot".DIRECTORY_SEPARATOR."css":"The css creation failed , Please check your permissions !";
						$is_create_img  = mkdir($name.DIRECTORY_SEPARATOR."DocumentRoot".DIRECTORY_SEPARATOR."img",0777);
						$msg_array[]    = $is_create_img?"new folder : {$name}".DIRECTORY_SEPARATOR."DocumentRoot".DIRECTORY_SEPARATOR."img":"The img creation failed , Please check your permissions !";
						$is_create_js   = mkdir($name.DIRECTORY_SEPARATOR."DocumentRoot".DIRECTORY_SEPARATOR."js",0777);
						$msg_array[]    = $is_create_js?"new folder : {$name}".DIRECTORY_SEPARATOR."DocumentRoot".DIRECTORY_SEPARATOR."js":"The js creation failed , Please check your permissions !";
					}
					_success($msg_array);
				}else{ 
					_error('The File creation failed , Please check your permissions !');
				}
			}else{ 
				_error('The current is not the root directory !');
			}
			
		}
	}else{ 
		_error('Must be the name of the project !');
	}	
}

function isroot(){ 
	if(file_exists(BASE_PATH.INIT_NAME)){ 
		return true;
	}else{ 
		return false;
	}
}

function scaffold($argv){ 
	$scaffold      = explode(":",$argv[2]);
	$project_name = $argv[1];
	$project      = explode(",",PROJECT_LIST);
	if($scaffold[0]=="scaffold" && $scaffold[1]!=="" ){ 
		if(in_array($project_name,$project)){ 
			$sname = $scaffold[1];
			unset($argv[0]);
			unset($argv[1]);
			unset($argv[2]);
			$_array=array_values($argv);
			$count=count($_array);
			createController($project_name,$sname);
			if($count>0){ 
				if(in_array("-s",$_array) || in_array("-srv",$_array)){ 
					createService($sname);
				}

				if(in_array("-d",$_array) || in_array("-dao",$_array)){ 
					createDao($sname);
				}
			}
		}else{ 
			_error('Please input in the name of the project , for example "'.PROJECT_LIST.'"');
		}		
		

	}
}

function createController($project,$name,$func=array()){ 
	if(file_exists($project.DIRECTORY_SEPARATOR.CONTROLLER_NAME)){ 
		$request  ='$request';
		$response ='$response';	
		$ctime    = date("Y-m-d H:i:s",time());
		$extends_controller=$project=="admin"?"BaseController":"AppBaseController";
		$func_string = "";
		checkUpper($name);
		$file_name   = strtolower($name);

		foreach($func as $key=>$val){ 
			$func_string.="    "."public function {$val}({$request}, {$response}){}"."\r\n";
		}

$content = <<<EOF
<?php

/**
 * @author yourname@daling.com
 * @date {$ctime}
 * @desc 
 */

namespace admin\controller;

use app\common\util\SubPages;

class {$name}Controller extends {$extends_controller}{
{$func_string}

EOF;

		$msg_array = array();
		if (!file_exists($project.DIRECTORY_SEPARATOR.CONTROLLER_NAME.DIRECTORY_SEPARATOR.$file_name.CONTROLLER_NAME.".php")) {
			$file   = $project.DIRECTORY_SEPARATOR.CONTROLLER_NAME.DIRECTORY_SEPARATOR.$file_name.CONTROLLER_NAME.".php";
			$handle = fopen($file, "w"); 
			chmod($project.DIRECTORY_SEPARATOR.CONTROLLER_NAME,0777);
			if($handle){ 
				$cont = fwrite($handle, $content);
				if($cont === FALSE){  
		            _error("Cannot write to {$name}controller file , Please check your permissions !");  
		        }else{ 		        	
		        	$msg_array[]="new file : {$file}";
		        	if(!file_exists($project.DIRECTORY_SEPARATOR."template".DIRECTORY_SEPARATOR.$name)){ 
		        		$create_template = mkdir($project.DIRECTORY_SEPARATOR."template".DIRECTORY_SEPARATOR.$name,0777);
		        		$msg_array[]     = $create_template?"new folder : {$name}".DIRECTORY_SEPARATOR."template".DIRECTORY_SEPARATOR.$name:"The template {$name} creation failed , Please check your permissions !";
		        		_success($msg_array);
		        		if($create_template){ 
		        			createTemplates($project.DIRECTORY_SEPARATOR."template".DIRECTORY_SEPARATOR.$name);
		        		}
		        	}else{ 
		        		_success($msg_array);
		        	}
		        	
		        }   
			} 
		}else{ 
			_error("This controller {$name}controller is already exists !");
		}


	}
}

function updateController($project,$name,$func=array()){
	checkUpper($name);
	$file_name   = strtolower($name);
	$func_string = "";
	$request  ='$request';
	$response ='$response';	

	$msg_array = array();
	if (file_exists($project.DIRECTORY_SEPARATOR.CONTROLLER_NAME.DIRECTORY_SEPARATOR.$file_name.CONTROLLER_NAME.".php")) { 
		$file = $project.DIRECTORY_SEPARATOR.CONTROLLER_NAME.DIRECTORY_SEPARATOR.$file_name.CONTROLLER_NAME.".php";

		$controller_data=file_get_contents($file);
		foreach($func as $key=>$val){
			 $func_line="function ".$val."("; 
			 if(!strstr($controller_data,$func_line)){ 
			 	$func_string.="    "."public function {$val}({$request}, {$response}){}"."\r\n\r\n";
			 }else{ 
			 	_error("This function {$val} is already exists !");
			 	exit();
			 }			
		}
		
		$handle = fopen($file, "a+"); 
		chmod($project.DIRECTORY_SEPARATOR.CONTROLLER_NAME,0777);
		if($handle){ 
			$cont = fwrite($handle, $func_string);
			if($cont === FALSE){  
		        _error("Cannot write to {$name}controller file , Please check your permissions !");  
		    }else{ 
		    	$msg_array[]="update file : {$file}";
		    	_success($msg_array);
		    }
		}
	}
}

function createDao($name){ 
	//var_dump("hello");die;
	if($name!==""){ 
		checkUpper($name);
		$file_name= strtolower($name);
		$file=APP_PATH_NAME.DIRECTORY_SEPARATOR.DAO_NAME.DIRECTORY_SEPARATOR.$file_name."dao.php";
 
		$obj    = '$this';
		$_master= '$_master';
		$_slave = '$_slave';
		$params = '$params';
		$limit  = '$limit';
		$sort   = '$sort';
		$str    = '$str';
		$where  = '$where';
		$sql    = '$sql';
		$ctime  = date("Y-m-d H:i:s",time());

$content = <<<EOF
<?php

/**
 * @author yourname@daling.com
 * @date {$ctime}
 * @desc 
 */

namespace app\dao;

class {$name}BlackListDao extends YmallDao {
	protected static $_master;
	protected static $_slave;
	public function getTableName() {
		return 'ym_';
	}

	public function getPKey() {
		return 'id';
	}

	public function getList( {$params}, {$limit} = '0,9', {$sort} = 'id ASC' ) {
        {$sql} = "SELECT * FROM " . self::getTableName () . " WHERE " . self::makeSql ( {$params} ) . " ORDER BY " . {$sort} . " LIMIT " . {$limit};
		return {$obj}->_pdo->getRows ( {$sql} );
	}

	public function getListCnt({$params}) {
		{$sql} = "SELECT COUNT(*) FROM " . self::getTableName () . " WHERE " . self::makeSql ( {$params} );
		return {$obj}->_pdo->getOne ( {$sql} );
	}

    public function getListAll({$params}, $sort = 'id ASC') {
        {$sql} = "SELECT * FROM " . self::getTableName () . " WHERE " . self::makeSql ( {$params} ) . " ORDER BY " . {$sort};
        return {$obj}->_pdo->getRows ( {$sql} );
    }

    public function getInfo( {$params} ) {
        {$sql} = "SELECT * FROM " . self::getTableName () . " WHERE " . self::makeSql ( {$params} );
        return {$obj}->_pdo->getRow ( {$sql} );
    }

    public function updateByWhere( {$params} = array(), {$str}='' ) {
        {$sql} = " UPDATE " . {$obj}->getTableName() . " SET " . {$str} . " WHERE " . self::makeSql({$params});
        return {$obj}->_pdo->exec({$sql});
    }
    
    public function getExist({$where}=''){
        {$sql} = "SELECT COUNT(*) FROM " . self::getTableName () . " WHERE " .{$where};
        return {$obj}->_pdo->getOne({$sql});
    }
    
	private function makeSql({$params}) {
		if (is_array ( {$params} ) && count ( {$params} ) > 0) {
			return implode ( ' AND ', {$params} );
		} else {
			return '1';
		}
	}
}
EOF;
		
		$msg_array = array();
		if(!file_exists($file)){ 
			$handle = fopen($file, "w");
			chmod(APP_PATH_NAME.DIRECTORY_SEPARATOR.DAO_NAME,0777);
			if($handle){ 
				$cont = fwrite($handle, $content);
				if($cont === FALSE){ 
					_error("Cannot write to {$name}dao file , Please check your permissions !"); 
				}else{ 
					$msg_array[]="new file : {$file}";
				} 
				_success($msg_array);
			}
		}else{ 
			_error("This Dao for {$name}dao is already exists !");
		}
	}
}

function createService($name){ 
	if($name!==""){ 
		checkUpper($name);
		$file_name= strtolower($name);
		$file   = APP_PATH_NAME.DIRECTORY_SEPARATOR.SERVICE_NAME.DIRECTORY_SEPARATOR.$file_name."srv.php";
		$id     = '$id';
		$params ='$params';
		$limit  = '$limit';
		$sort   = '$sort';
		$str    = '$str';
		$where  = '$where';
		$ret    = '$ret';
		$data   = '$data';
		$ctime  = date("Y-m-d H:i:s",time());

$content = <<<EOF
<?php

/**
 * @author yourname@daling.com
 * @date {$ctime}
 * @desc 
 */

namespace app\service;
use app\dao\\{$name}Dao;

class {$name}Srv extends BaseSrv {
 
    public function getListCnt({$params}=array())
    {
        return {$name}Dao::getSlaveInstance()->getListCnt({$params});
    }

    public function getList( {$params}=array(), {$limit}='0,9', {$sort} = 'id ASC' )
    {
        return {$name}Dao::getSlaveInstance()->getList( {$params}, {$limit}, {$sort} );
    }

    public function getListAll( {$params}=array(), {$sort} = 'id ASC' )
    {
        return {$name}Dao::getSlaveInstance()->getListAll( {$params}, {$sort} );
    }

    public function getInfo( {$params}=array() )
    {
        return {$name}Dao::getSlaveInstance()->getInfo( {$params} );
    }

    public function addRecord( {$params}=array() )
    {
        {$ret} = 0;
        if({$params}){
            $ret = {$name}Dao::getMasterInstance()->add( {$params} );
        }
        return {$ret};
    }

    public function editRecord({$id}='', {$data}=array())
    {
        {$ret} = 0;
        if( {$id}>0 && is_array({$data}) ){
            {$ret} = {$name}Dao::getMasterInstance()->edit({$id}, {$data});
        }
        return {$ret};
    }

    public function updateByWhere( {$params} = array(), {$str}='' )
    {
        return {$name}Dao::getMasterInstance()->updateByWhere( {$params}, {$str} );
    }

    public function delRecord( {$where}='' )
    {
        {$ret} = 0;
        if( {$where} ){
            {$ret} = {$name}Dao::getMasterInstance()->deleteByWhere({$where});
        }
        return {$ret};
    }
 
}	
EOF;
	
		$msg_array = array();
		if(!file_exists($file)){ 
			$handle = fopen($file, "w");
			chmod(APP_PATH_NAME.DIRECTORY_SEPARATOR.SERVICE_NAME,0777);
			if($handle){ 
				$cont = fwrite($handle, $content);
				if($cont === FALSE){ 
					_error("Cannot write to {$name}srv file , Please check your permissions !"); 
				}else{ 
					$msg_array[]="new file : {$file}";
				} 
				_success($msg_array);
			}
		}else{ 
			_error("This Service for {$name}srv is already exists !");
		}

	}
}

function createTemplates($path=''){ 
	$msg_array = array();
	if($path!=="" && file_exists($path)){
		chmod($path,0777); 
		$template=array("index.html","list.html","detail.html","edit.html");
		foreach($template as $key=>$val){ 
			$handle = fopen($path.DIRECTORY_SEPARATOR.$val, "a+");
			if($handle){ 
				$msg_array[]="new file : ".$path.DIRECTORY_SEPARATOR.$val;
			}
		}
		_success($msg_array);
	}else{ 
		_error("The template path must be exists !");
	}
}

function cache($argv,$type){ 
	switch($type){ 
		case $type=='clear' && isset($argv[1]) && isset($argv[2]):
		    $tmp        = BASE_PATH."..".DIRECTORY_SEPARATOR.CACHE_ROOT_NAME;     
		    $cache_file = $tmp.DIRECTORY_SEPARATOR.$argv[1].DIRECTORY_SEPARATOR."templates_c"; 
                
		    if(file_exists($tmp) && file_exists($cache_file)){ 
		    	$dh=opendir($cache_file);
		    	$msg_array = array();
		    	while ($file=readdir($dh)){ 
		    		if($file!="." && $file!="..") { 
		    			$fullpath=$cache_file.DIRECTORY_SEPARATOR.$file;
		    			if(!is_dir($fullpath)) {
		    				chmod($fullpath, 0666);
					        unlink($fullpath);
					        $msg_array[]="delete file : {$file}";
					    } 
		    		}
		    	}
		    	_success($msg_array);
		    }else{ 
		    	_error("The cache directory may not exist !");
		    }                         
			break;
		default :
			other_error($argv);
			break;	
	}
}

function help(){ 
	$msg_array = array();
	$msg_array[]="php smart new:project [name]";
	$msg_array[]="php smart [projectname] scaffold:[name] [-s|-srv] [-d|dao]";
	$msg_array[]="php smart [projectname] add:[controller|c] [name] [func1 func2 func 3 func 4 ......]";
	$msg_array[]="php smart add:[srv|-s] [name]";
	$msg_array[]="php smart add:[dao|-d] [name]";
	$msg_array[]="php smart [projectname] cache:clear";
	$msg_array[]="php smart -h or help";
	_success($msg_array);
}

function other_error($argv){ 
	$argv_str=implode("||", $argv);
	$line["scaffold"]        = preg_match("/scaffold/",$argv_str);
	$line["controller_add"] = preg_match("/add:controller/",$argv_str);
	$line["dao_add"]        = preg_match("/add:dao/",$argv_str);
	$line["service_add"]    = preg_match("/add:srv/",$argv_str);

	$line["c_add_short"]   = preg_match("/add:c/",$argv_str);
	$line["s_add_short"]   = preg_match("/add:s/",$argv_str);
	$line["dao_add_short"] = preg_match("/add:d/",$argv_str);
	$line["cache_clear"]   = preg_match("/cache:clear/",$argv_str);
	switch($line){ 
		case $line["scaffold"]: 
			_error("your command has some wrong , you can try php smart [yourproject] scaffold:[name] [-s or -srv] [-d or -dao]");
			break;
		case $line["controller_add"]: 
			_error("your command has some wrong , you can try php smart [yourproject] add:[controller or c] [func1 func2 func3 .......]");
			break;
		case $line["dao_add"]: 
			_error("your command has some wrong , you can try php smart add:[dao or d] [name]");
			break;
		case $line["service_add"]: 
			_error("your command has some wrong , you can try php smart add:[srv or s] [name]");
			break;
		case $line["c_add_short"]: 
			_error("your command has some wrong , you can try php smart [yourproject] add:[controller or c] [func1 func2 func3 .......]");
			break;
		case $line["s_add_short"]: 
			_error("your command has some wrong , you can try php smart add:[srv or s] [name]");
			break;	
		case $line["dao_add_short"]: 
			_error("your command has some wrong , you can try php smart add:[dao or d] [name]");
			break;	
	    case $line["cache_clear"]: 
			_error("your command has some wrong , you can try php smart [yourproject] cache:clear");
			break;		
		default :
			_error("you command has some wrong or undefined params , you can input 'php smart -h' to help");
			break;						
	}

}

function checkUpper($name=""){ 
	if($name){ 
		$U=0;
		for($i=0;$i<strlen($name);$i++){ 
			$s=substr($name,$i,1);
			if(preg_match('/^[A-Z]+$/', $s)){
        		$U++;
    		}
		}
		if($U>=1 && $U<strlen($name)){ 
			return true;
		}else{ 
			_error("This name is whether to follow Camel-Case ? Please check it .");
			exit();
		}
	}else{ 
		_error("This name maybe has error for Camel-Case");
		exit();
	}
}

function _success($msg){ 
	$msg_string="";
	foreach($msg as $key=>$val){ 
		$msg_string.="        ".$val."\r\n";
	}
$lines = <<<EOF
{$msg_string}
EOF;
	fwrite(STDOUT, "{$lines}");

}

function _error($msg){
	$msg="        ".$msg."\r\n"; 
$lines = <<<EOF
{$msg}
EOF;
	fwrite(STDOUT, "{$lines}"); 	
}



run();

?>