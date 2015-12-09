<?php
/**
 * 관리자 설정 페이지 공통 추상 클래스.
 *
 * @author  cafe24
 * @copyright 2015.11.01
 * @version 1.0
 */
abstract class adminDisp extends Controller_Admin
{

    /**
     * 공통으로 사용하는 인자값 배열을 가진다.
     *
     * @var array
     */
    public $args = NULL;


    /**
     * 처음 실행되고 실행되는 클래스
     *
     * @param array $aArgs
     */
    protected function run( $aArgs )
    {
        // 파라메타 정의
        $this->args = $aArgs;

        // 템플릿 tpl 파일 경로 파일 경로를 반환.
        $sView = $this->execute();

        // tpl 파일 경로를 단계별로 변수로 할당 ( ex : admin/setup -> [0]=>'admin', [1]=>'setup' )
        if ( !empty( $sView) ) {
            $aTopMenuVal = explode( "/", $sView );
            $this->assign('depth1' , $aTopMenuVal[0]);
            $this->assign('depth2' , $aTopMenuVal[1]);
        }

        // 기본 Cstore UI Javascript, CSS 파일 정의
        $this->importJsCss();

        // 템플릿 파일에 할당될 변수 지정
        $this->assign('mall_id', $this->Request->getDomain());

        // 템플릿 파일 호출
        $this->setLayout('default');    // 기본 레이아웃 파일 호출 ( /resource/layout/default.tpl )
        $this->view('common/topmenu','topmenu');    // 상단에 노출될 공통 탭 메뉴, topmenu 변수로 할당 ( /resource/tpl/common/topmenu.tpl )
        $this->view($sView,'contents'); // 관리자 설정 페이지, contents 변수로 할당 ( / resource/tpl/*.tpl )
    }



    /**
     * CStore에서 제공하는 기본 UI Javascript, CSS Import
     */
    protected function importJsCss()
    {
        // 외부 CSS 파일 Load
        $this->externalCSS('http://img.echosting.cafe24.com/smartAdmin/css/module.css');
        $this->externalCSS('http://img.echosting.cafe24.com/smartAdmin/css/myapps.css');
        $this->externalJS('http://img.echosting.cafe24.com/js/suio.js');

        // 앱 내부 파일 Load ( /resource/js/default.js )
        $this->importJS('default');
    }



    /**
     * 배열을 풀어서 assign 하기
     *
     * @param array $aConfig
     */
    protected function arrayAssign( $aConfig )
    {
        foreach( $aConfig as $sKey => $sVal ) {
            $this->assign( trim($sKey), $sVal);
        }
    }

    /**
     * 기본 하위 로직의 실행.
     */
    abstract public function execute();

}
