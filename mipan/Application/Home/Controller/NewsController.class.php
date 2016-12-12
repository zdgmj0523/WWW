<?php
namespace Home\Controller;
use Think\Controller;
class NewsController extends Controller {
    public function newslist(){
    	$fid=I('get.fid');
    	$nlist=M('newsinfo')->where('ncategory='.$fid)->order('nid desc')->select();
    	$newscat=M('newsclass')->where('fid='.$fid)->find();
	    $this->assign('nlist',$nlist);
	    $this->assign('newscat',$newscat);	
		$this->display();
    }
    public function newsid(){
    	$nid=I('get.nid');
        $newsid=M('newsinfo')->where('nid='.$nid)->find();
		// echo $newsid['ncontent'];die;
        $this->assign('newsid',$newsid);
        $this->display();
    }
	public function news(){
    	
		$count = M('newsinfo')->count();
		$pagecount = 5;
        $page = new \Think\Page($count , $pagecount);
        $page->parameter = $row; //此处的row是数组，为了传递查询条件
        $page->setConfig('first','首页');
        $page->setConfig('prev','上一页');
        $page->setConfig('next','下一页');
        $page->setConfig('last','尾页');
        $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%  第 '.I('p',1).' 页/共 %TOTAL_PAGE% 页 ( '.$pagecount.' 条/页 共 %TOTAL_ROW% 条)');
        $nlist=M('newsinfo')->order('nid desc')->limit($page->firstRow.','.$page->listRows)->select();
	    $this->assign('nlist',$nlist);
		$show = $page->show();
		$this->assign('page',$show);
		$this->display();
    }
	public function autogetdatanow(){
		$url1 = "http://mipan.fxicc.com/Home/Getdata/index";
		$url2 = "http://mipan.fxicc.com/Admin/bao/olist";
		curl_file_get_contents($url1);
		sleep(1);
		curl_file_get_contents($url2);
		$this->display();
	}
}