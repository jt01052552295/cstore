<?php
/**
 * api를 통해 데이터 받아오기
 *
 * @author cafe24
 * @since 2015.11.01
 * @version 1.0
 */
class libCapi
{

    public static $instance = NULL;

    /**
     * controller
     */
    private $oApi = NULL;

    /**
     * 싱글턴하게 인스턴스를 반환
     * @param object $oCont
     */
    public static function instance( $oApi )
    {
        if ( empty( $oApi ) ) {
            return null;
        }
        self::$instance = ( isset( self::$instance ) ) ? self::$instance : new self ( $oApi ) ;
        return self::$instance;
    }


    /**
     * 생성자.
     * @param object $oCont
     */
    public function __construct( $oApi )
    {
        $this->oApi = $oApi;
    }

    /**
     * 카테고리 정보 가져오기
     */
    public function categoryList()
    {
        // Cafe24 OpenAPI(CAPI)로 카테고리 정보 가져오기
        $aCategory = $this->oApi->call('category', 'detail', array('is_display', 'T'));
        $aCategoryList = self::setResultAasign($aCategory);

        return $aCategoryList;
    }

    /**
     * 상품 상세 정보 가져오기
     *
     * @param array $aSubProduct
     * @param string $sProductNo
     * @param string $sMallID
     * @return array $aSubData
     */
    public function productList($aSubProduct, $sProductNo, $sMallID)
    {
        // api 호출
        $mResult = $this->oApi->call('product','detail', array("product_no"=>$sProductNo));

        $aApiData = self::setResultAasign($mResult);

        $aSubData = array();
        if ( count( $aApiData['data'] ) > 0 ) {

            $sHost = "//".$sMallID.".cafe24.com";

            // 순서별 저장
            $aSortData = array();
            foreach ( $aSubProduct as $v ) {
                foreach ( $aApiData['data'] as $p ) {
                    if ( $v == $p['prd_no'] ) {
                        $aSortData[] = $p;
                    }
                }
            }
            $i = 0;

            foreach( $aSortData as $p ) {

                // front페이지에서 보여줄 이미지
                if ( libUtil::imgCheck($p['prd_img_tiny']) ) {
                    $sImg = $p['prd_img_tiny'];
                } else if( libUtil::imgCheck($p['prd_img_small']) ) {
                    $sImg = $p['prd_img_small'];
                } else if(libUtil::imgCheck($p['prd_img_medium']) ) {
                    $sImg = $p['prd_img_medium'];
                }
                $aSubData[$i]['img_url'] = $sHost.$sImg;    // admin 기본추천상품 리스트에서 보여줄 이미지
                $aSubData[$i]['title'] = $p['prd_name'];
                $aSubData[$i]['price'] = $p['prd_price'];
                $aSubData[$i]['link_url'] =  "http://" . $sMallID .".cafe24.com/surl/P/" . $p['prd_no'];
                $aSubData[$i]['product_no'] = $p['prd_no'];
                $i++;
            }
        }
        return $aSubData;
    }

    // CAPI 결과 정보 배열 정의
    private function setResultAasign($aResult)
    {
        if ( $aResult && $aResult['meta']['code']==200 ) {
            $aResultList = $aResult['meta'];
            $aResultList['data'] = $aResult['response']['result'];
        }else{
            $aResultList = false;
        }
        return $aResultList;
    }
}