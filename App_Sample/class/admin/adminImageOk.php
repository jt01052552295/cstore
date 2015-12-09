<?php
/**
 * 이미지 파일 업로드 처리
 *
 * @author cafe24
 * @copyright 2015.12.01
 * @version 1.0
 */
class adminImageOk extends Controller_AdminExec
{
    /**
     * 실행
     *
     * @param array $aArgs
     */
    public function run( $aArgs )
    {
        // 배열 변수 초기화
        $aData = array();

        // 파일 업로드 처리
        $aData = $this->doUploadImage();

        if ( !empty( $aData )  ) {
            // 이전에 저장된 이미지 삭제
            if ( is_array( $aOldImage = $this->Redis->get( libConfig::APP_IMAGE ) ) ){
                $this->Storage->unlink( $aOldImage['m_upload_path'] . $aOldImage['m_upload_name'] );
            }

            // 업로드된 이미지 경로 Redis에 저장
            if( $this->Redis->set( libConfig::APP_IMAGE, $aData ) < 1 ) {
                $this->writeJs("alert('저장중에 문제가 발생하였습니다.\n다시한번 확인하여 주세요.'); location.href='[link=admin/Image]';");
            }else{
                $this->writeJs("location.href='[link=admin/Image]';");
            }
        } else {
            $this->writeJs("alert('저장중에 문제가 발생하였습니다.\n다시한번 확인하여 주세요.'); location.href='[link=admin/Image]';");
        }

        return $aData;
    }

    /**
     * 파일 업로드 처리
     *
     * @return array $aData
     */
    private function doUploadImage()
    {
        // 배열 변수 초기화
        $aData = array();

        // 업로드 클래스 객체 생성
        $oUpload = libUpload::instance( $this->Upload, $this->Storage );

        // 이미지 업로드 처리
        $aUpload = $oUpload->execute('imgFile');

        // 업로드 후 return 된 정보 할당
        if ( !empty( $aUpload['filename'] ) ) {
            $sImg = $aUpload['upload_path'] . $aUpload['upload_name'];
            list( $sWidth, $sHeight) = $oUpload->imgSize($sImg);
            list( $sWidth, $sHeight ) = libUtil::checkImgSize( $sWidth, $sHeight );
            $aData['m_img_realname'] = $aUpload['filename'];
            $aData['m_img_width'] = $sWidth;
            $aData['m_img_height'] = $sHeight;
            $aData['m_upload_path'] = $aUpload['upload_path'];
            $aData['m_upload_name'] = $aUpload['upload_name'];
            $aData['m_reg_date'] = date('Y-m-d H:i:s');
        }

        return $aData;
    }

}