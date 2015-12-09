<?php
/**
 * 사용설정 화면
 *
 * @author cafe24
 * @since 2015.11.01
 * @version 1.0
 */
class adminIndex extends adminDisp
{
    /**
     * 최초 실행 화면
     * @return string tpl 반환
     */
    public function execute()
    {

        // 카테고리 캐시 정보가 없으면 정보 가져오기
        if ( $this->Redis->ttl( libConfig::APP_CATEGORY_CACHE ) < 0 || isset($this->args['c']) ) {

            // Cafe24 OpenAPI(CAPI)로 카테고리 정보 가져오기
            $aCategoryList = libCapi::instance($this->Openapi)->categoryList();

            if ( count( $aCategoryList['data']) > 0 ) {
                // 카테고리 단계별로 트리형 배열로 반환
                $aCate = libCategory::tree( $aCategoryList );
                // CAPI를 통한 카테고리 정보 Redis에 저장
                $this->Redis->set( libConfig::APP_CATEGORY_CACHE, $aCate );
                // 86,400초(60초*60분*24시간) 동안 만료시간 지정
                $this->Redis->expire( libConfig::APP_CATEGORY_CACHE, libConfig::EXPIRE_TIME);
            }
        } else {
            // 카테고리 캐시 정보가 있다면 Redis 정보 가져오기
            $aCate = $this->Redis->get(libConfig::APP_CATEGORY_CACHE);
        }

        // 저장된 설정 정보 가져오기
        $aSetupData = $this->Redis->get( libConfig::APP_SETUP );

        // 설정 정보 동일한 이름으로 변수에 할당
        if ( !empty($aSetupData) ) {

            $this->arrayAssign( $aSetupData );
            if ( !empty($aSetupData['products']) ) {
                $aSetupData['p_nos'] = $aSetupData['products'];
            }

            $this->assign( 'addProductNum', explode("|", $aSetupData['p_nos']) );
        };

        // 저장된 설정 정보가 없을 경우 기본 정보 설정
        if ( empty($aSetupData['pChoice']) ) {
            $this->assign( 'defaultPchoice', "checked='checked' "); // 상품 선택 방식 초기화
            $this->assign( 'defaultDisplay', 'displaynone');    // 기본 출력 상품 설정 초기화
        }

        // 변수 할당
        $this->assign('pChoice', $aSetupData['pChoice']);
        $this->assign('settings', $aSetupData );
        // 카테고리 정보 변수 할당
        $this->assign('CategoryData', $aCate );

        // Javascript, CSS 파일 정의
        $this->importCss('default');    // 호출 경로 : resource/css/default.css
        $this->importJS('admin_category', array()); // 호출 경로 : resource/js/admin_category.js
        $this->importJS('admin_setting', array()); // 호출 경로 : resource/js/admin_setting.js
        $this->importJS('setup');   // 호출 경로 : resource/js/setup.js

        // 최종적으로 tpl 파일 경로 반환( /resource/tpl/setup.tpl )
    	return 'setup';
    }
}
