<?php
/**
 * 이미지 업로드 설정
 *
 * @author cafe24
 * @copyright 2015.11.01
 * @version 1.0
 */
class adminImage extends adminDisp
{
    /**
     * 최초 실행 화면
     * @return string tpl 반환
     */
    public function execute()
    {
        // Redis에서 서버에 저장된 이미지 경로 가져오기
        $aImageData = $this->Redis->get( libConfig::APP_IMAGE );

        // Javascript, CSS 파일 정의
        $this->importCss('default');    // 호출 경로 : /resource/css/default.css
        $this->importJS('image'); // 호출 경로 : /resource/js/image.js

        // 서버에 저장된 이미지 경로 할당
        if( !empty($aImageData['m_upload_name']) ){
            $sImgUrl = '<img src="'.libUtil::pFilePath($aImageData['m_upload_path'].$aImageData['m_upload_name']).'" style="max-width: 700px;">';
        }
         $sImgUrl=libUtil::pFilePath($aImageData['m_upload_path'].$aImageData['m_upload_name']);
        $this->assign('pc_upload_path', $sImgUrl);
        // 최종적으로 tpl 파일 경로 반환( /resource/tpl/image.tpl )
    	return 'image';
    }
}
