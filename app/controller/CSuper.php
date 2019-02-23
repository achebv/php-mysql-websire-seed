<?php


class CSuper extends CBase{


	public $cname = "super";
	
	public $famInactive = [];

	/**
	 * Default Controller initialization
	 * @see ICApp::init()
	 */
	public function init(){
		$this->jsFiles[] = 'js/app/' . $this->cname;
    //    $this->cssFiles[] = 'css/' . $this->cname;
	}


	/**
	 * Common tasks
	 */
	public function before(){
		//	render admin page
		if($this->auth->IsSuper()){
			$page = 'dashboad';
			$seg = $this->getSegment(1);
			$file = APP_PATH . TEMPLATE_PATH . SKIN_NAME . '/' . $this->cname . '/' . $seg . '.phtml';
			if(file_exists($file)){
				$page = $seg;
			}
		}else{
			$page = 'login';
		}
		$this->renderBox = $page;
	}


	/**
	 * Index action
	 */
	public function index(){
	//	die('a');
	}


	/**
	 * backup currently db into backup folder
	 * read files from /sql/deploy/ folder
	 * deploy and move file to sql/deployed/ folder
	 * create db objects
	 */
	public function db(){
		$hf = $this->storeObject('HFile', 'hFile');
		if(!$this->isPostBack()){
			$this->files = $hf->getFilesFromFolder(APP_PATH . 'misc/sql/deploy/');
		}else{
			$this->setResponseFormat('json');
			$msg = '<ul>';
			$hasDBO = false;
			$dbTool = $this->storeObject('HDbTool', 'hdbTool');
			if($this->files>0){
				$dbTool->backup();
				$msg .= '<li>A database backup was created.</li>';
				$files = $hf->getFilesFromFolder(APP_PATH . 'misc/sql/deploy/');
				foreach($files as $file){
					$msg .= '<li>File "'.$file .'" successful imported. </li>';
					$dbTool->restore($file, false);
					$pref = date("Ymd_His") . '_';
					rename(APP_PATH . 'misc/sql/deploy/' . $file, APP_PATH . 'misc/sql/deployed/' . $pref . $file);
				}
				if(isset($this->dbo) && $this->dbo==1){
					$hasDBO = true;
				}
			}else{
				$hasDBO = true;
			}

			if($hasDBO){
				$msg .= '<li>DBO Classes created. </li>';
				$dbTool->dbo();
			}
			$this->err = false;
			$this->msg = $msg . '</ul>';
			//$this->url = $this->openPage('Super', 0, true) . 'db/';
		}
	}


	/**
	 * list management
	 */
	public function user(){
		if(!$this->isPostBack()){
			//$this->famActive = DaoFamily::finder()->findAllByIsActive(1);
			$this->famInactive = DaoFamily::finder()->findAllByIsActive(0);
		}else{
			$this->setResponseFormat('json');
			$fam = DaoFamily::finder()->findByFamilyID($this->getPost('FamilyID'));
			$fam->IsActive = 1;
			$fam->save();
			$bc = $this->storeObject('BCategory', 'BCategory');
			$bc->importCat($this->getPost('FamilyID'));
			$emailData = DaoUser::finder()->findByFamilyIDAndIsOwner($this->getPost('FamilyID'), 1);
			$t = $this->storeObject('Tools', 'tools');
			$t->sendEmail($emailData->Email, "BUGET APP: Cont activat.",
				array(
					'content' => " Contul dumneavoastra a fost activat. "
				)
			);
			$this->err = false;
			$this->url = $this->openPage('Super', 0, true) . 'user/';
		}
	}

}