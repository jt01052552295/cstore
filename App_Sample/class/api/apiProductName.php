<?php
/**
 * 상품명 검색 정보 호출 api
 *
 * @author cafe24
 * @since 2015.11.01
 * @version 1.0
 */
class apiProductName extends apiSdk
{
    public function execute()
    {
        // 넘겨받은 파라메타 설정
        $sSearchName = $this->args['search_name'];
        $iSortMethod = $this->args['sortMethod'];
        $iCount = 30;

        // 검색 키워드가 없다면 실행 종료
        if ( empty($sSearchName) ) {
            return false;
        }

        // 상품 CAPI [검색정보] 호출
        $aProductInfo = $this->Openapi->call(
                'product',
                'search',
                array(
                        'search_val'=>$sSearchName,
                        'sort'=>$iSortMethod,
                        'page'=>1,
                        'limit'=>$iCount
                )
        );

        $aProduct = $aProductInfo['meta'];
        $aProduct['data']['total_record']  = $aProductInfo['response']['total_record'];
        $aProduct['data']['products']  = $aProductInfo['response']['result'];

        // 정상적으로 데이타 받았을 경우 상태코드, 메세지, 상품정보 배열에 저장
        if ( isset($aProduct['data']) && count($aProduct['data']['products']) > 0 ) {
            $this->setStatusCode($aProduct['code']);
            $this->setMessage($aProduct['message']);
            $aData =  $aProduct['data'];
        }

        return $aProduct;
    }

}
