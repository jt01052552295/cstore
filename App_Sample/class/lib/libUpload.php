<?php
/**
*  SDK 업로드
*
* @author cafe24
* @since 2015.11.01
* @version 1.0
*/
class libUpload
{
    static public $oInstance;

    static public function instance( $oUploader, $oStorage )
    {
        self::$oInstance = ( isset( self::$oInstance ) ) ? self::$oInstance : new libUpload( $oUploader, $oStorage ) ;
        return self::$oInstance;
    }

    private $_oUploader;

    private $_oStorage;

    /**
     * 생성자
     * @param object $oUploader
     * @param object $oStorage
     */
    private function __construct( $oUploader, $oStorage )
    {
        $this->_oUploader = $oUploader;
        $this->_oStorage = $oStorage;
        return $this;
    }

    /**
     * 파일 업로드 실행
     *
     * @param string $sFileName
     */
    public function execute( $sFileName )
    {
        $aFile = $this->_oUploader->uploadedFiles();

        if ( isset( $aFile[ $sFileName ] ) ) {
            $aImage = $aFile[ $sFileName ];
            $aInfo = pathInfo( $aImage['filename'] );
            if ( !in_array( strtolower($aInfo['extension']), $this->possibleImgExt() ) ){
                return false;
            }

            //업로드 될 파일 실제 경로, 파일명 정보 배열에 저장
            $aImage['upload_name'] = md5(uniqid(rand(), true)) . "." . $aInfo['extension'];
            $aImage['upload_path'] = $this->uploadPath();

            //업로드 폴더 생성
            $this->makeDir( $aImage['upload_path'] );

            //임시폴더 업로드 후 지정 폴더로 이동
            $bResult = $this->_oUploader->moveUploadedFile($aImage['tmpname'], '/',  $aImage['upload_path'] . $aImage['upload_name'] );
            if ( $bResult ) {
                return $aImage;
            }
        }
        return false;
    }

    /**
     * 업로드 이미지 확장자 배열
     */
    private function possibleImgExt()
    {
        return  array('gif', 'jpg', 'jpeg', 'png', 'bmp');
    }

    /**
     * 업로드 기본 경로 반환
     */
    private function uploadPath()
    {
        return "public_files/" . date('Y') . "/" .date('m') . "/" . date('d') . "/";
    }

    /**
     * 파일 업로드 될 폴더(디렉토리) 유무 확인후 생성
     */
    private function makeDir( $sPath )
    {
        if ( !$this->_oStorage->is_dir( $sPath) ) {
            $this->_oStorage->mkdir( $sPath , true );
        }
    }

    /**
    * SDK에서 업로드된 이미지사이즈 얻어오지 못할때의 처리
    */
    public function imgSize( $sImg )
    {
        list($sWidth, $sHeight) = $this->_oStorage->getimagesize($sImg);
        if ( empty($sWidth) && empty($sHeight) ) {
            $tmpImgFileName = tempnam(sys_get_temp_dir(), APP_ID);
            $chkImgSize = file_put_contents( $tmpImgFileName, $this->_oStorage->file_get_contents($sImg) );
            if ( $chkImgSize !== false ) {
                list($sWidth, $sHeight) = getimagesize($tmpImgFileName);
            }
            @unlink($tmpImgFileName);
            return array( $sWidth, $sHeight );
        }
        return array( $sWidth, $sHeight );
    }
}