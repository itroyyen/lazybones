<?php
class DbTestController extends LZ_Controller{

	public function __construct(){
		parent::__construct();
	}

	public function Action_Index(){
		$user = new UserModel();
		$post = new PostModel();

		//使 Post 關聯至 User
		$user->relate($post);

		//設定Post的依照subject欄位排序
		$post->orderBy('{subject}');

		$user->find();
		$resultAll = $user->getAll();
		print_r($resultAll);

	}
	
	public function Action_Test(){
		app::loadModel('User');
		app::loadModel('UserInfo');
		$user = new UserModel();
		$userInfo = new UserInfoModel();
		
		$user->relationMode(LZ_Model::RELATE_NESTED);
		$user->relate($userInfo);
		echo $user->getFindSQL();
		echo "<br><br>";
		if($user->find()){
			echo "<br><br>";
			while($user->fetch()){
				echo $user->name."<br>";
				echo $userInfo->addr."<br>";
			}
		}else{
			echo app::db()->getLastSql()."<br>";
			echo "Not found! <br>";
		}

//		$userCnd = new UserModel();
//		$userCnd->name = array('like','%New Name%');
//		$user = new UserModel();
//		$user->name = 'New Name222';
//		$user->save($userCnd);
//		$user->reset();
//		$user->id = 3;
//		$user->name = '阿育2';
//		$user->save();

//		$user->findByAccountName('test','test');
//
//		$this->displayInfo();
//		echo app::_getTestUriList();
	}

	public function Action_Test2(){
		app::loadModel('User');
		app::loadModel('UserInfo');
		$user = new UserModel();
		
		if($user->find()){
			while($userRow = $user->get(LZ_Model::GET_OBJ)){
				$userRow->test = '11111';
				echo vdump($userRow)."<br>";
				
				echo "<br>";

			}
		}else{
			echo app::db()->getLastSql()."<br>";
			echo "Not found! <br>";
		}
	}

	public function Action_Test3(){
		$user = new UserModel();
		$userInfo = new UserInfoModel();
		$userInfo->prefix('ui');
		$userInfo->orderBy('{id} ASC');
		$user->relate($userInfo);
//		$user->orderBy('{UserInfo.id} DESC');
		echo $user->getFindSQL();
		echo "<br><br>";

		$user->find();
		$result = $user->getAll();

		vdump($result);
		
	}

	public function Action_Test4(){

		$userInfo = new UserInfoModel();
		$userInfo->email = 'test@test.com.tw';

		$user = new UserModel();
//		$user->name = '小田';
//		$user->account = 'tain';
//		$user->password = 'testtest';
//
//		$insterest = new InterestModel();
//		$insterest->label = "adsfasdf";
//		$insterest->id = 5;
//
//		$user->relate($insterest);
		$user->findById(5);
		$user->fetch();
		$user->id = null;
		$user->add();
		vdump($user->fields());
	}

	public function Action_Test5(){
		$user = new UserModel();
		$insterest = new InterestModel();
		
		$user->id = 3;
		$insterest->id = 5;
		$user->relate($insterest);
		$user->save();
	}

	public function Action_Test6(){
		$user = new UserModel();
		$post = new PostModel();
		$posCatg = new PostCategoryModel();
		$posCatg->label = '魔獸世界';
		$post->subject = 'Test';
		$user->id = 5;
		$user->relate($post->relate($posCatg));
		$user->save();
	}

	public function Action_Test7(){
		$user = new UserModel();
		$post = new PostModel();
		$posCatg = new PostCategoryModel();
		$posCatg->label = '魔獸世界';
		$post->id = 2;
		$user->id = 5;
		$user->relate($post->relate($posCatg));
		$user->save();
	}

	public function Action_Test8(){
		$user = new UserModel();
		$user->name = '小田';
		$user->account = 'tain';
		$user->password = 'testtest';

		$insterest = new InterestModel();
		$insterest->id = 2;
		$insterest->label = "adsfasdf";

		$user->relate($insterest);
		//$insterest 有指定id，因此只新增$user，並與$insterest關聯
		$user->add();
	}

	public function Action_Test9(){
		$user = new UserModel();
		$post = new PostModel();
		$posCatg = new PostCategoryModel();
		$posCatg->id = 2;
		$user->id = 5;
		$post->subject = 'new post!';
		$post->relate($posCatg)->relate($user);
		$post->add();

		
	}

	public function Action_Test10(){
		$user = new UserModel();
		$userInfo = new UserInfoModel();
		$insterest = new InterestModel();

//		$insterest->label = "coding";
//		$insterest->add();
//		$user->name = "Roy3";
//		$userInfo->email = 'royyam0812@gmail.com';
//		$user->relate(array($userInfo,$insterest));
//		$user->add();

//		$insterest->id = 1;
//		$user->where("name like '%Roy%'");
//		$user->relate($insterest);
//		$user->save();

//		$user->findByName('Roy1',true);

//		for($i = 0;$i<100;$i++){
//			$user->account = 'royyan'.$i;
//			$user->password = 'password'.$i;
//			$user->name = 'RoyYan('.$i.')';
//			$user->add();
//			$user->clearPrimary();
//		}
		
		$user->reset();
		$user->where("name like '%Roy%'");
		$user->find();

		$pg = new PaginationHelper($user);
		
		$pageInfo = $pg->getInfo(11, 2, 10);
		$pageList = $pg->getHtmlList(11, 2, 10);
		echo '';
		vprint($pageInfo);

		print $pageList;

		
		
//		$user->where("{name} like '%Roy%'");
//		$user->relate(array($userInfo,$insterest));
//		$user->delete();

	}

}