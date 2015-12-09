<?php
/**
 * 사용설정 정보 저장 처리
 *
 * @author cafe24
 * @since 2015.11.01
 * @version 1.0
 */
class adminIndexOk extends Controller_AdminExec
{
    /**
     * 공통으로 사용하는 몰 아이디 값
     * @var string
     */
    public $sMallID = NULL;

    /**
     * 정보 저장 프로세스
     * @return void
     */
    public function run( $aArgs )
    {
        // 변수 초기값 설정
        $bResult = false;

        // 접속된 MALL ID  가져오기
        $this->sMallID = $this->Request->getDomain();

        // 상품 코드가 있을 경우
        if ( !empty($aArgs['p_nos']) ) {
            // 50개 상품만 배열로 반환
            $aSubProduct = array_slice(explode(",", $aArgs['p_nos']), 0, 50);
            $sParam = implode("|", $aSubProduct);
            // 상품 상세 정보 가져오기
            $aArgs['defaultProducts'] = libCapi::instance( $this->Openapi )->ProductList($aSubProduct, $sParam, $this->sMallID);

            // 배열을 구분자 '|'으로 문자열로 변환
            $aArgs['p_nos'] = implode("|", $aSubProduct);
        }

        // 데이타 저장
        $bResult = $this->setData( $aArgs );

        if ( $bResult ) {
            $this->writeJs( 'alert("저장되었습니다."); location.href="[link=admin/index]"; ' );
            return true;
        } else {
            $this->writeJs( 'alert("저장중 오류가 발생 되었습니다."); location.href="[link=admin/index]"; ' );
            return true;
        }
    }


    /**
     * 레디스에 데이터 저장하기
     *
     * @param array $aArgs
     * @return boolean
     */
    private function setData( $aArgs )
    {
        // 저장될 시간 정보 배열 저장
        $aArgs['mtime'] = time();

        return $this->Redis->set( libConfig::APP_SETUP, $aArgs);
    }
}
