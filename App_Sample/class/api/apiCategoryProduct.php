<?php
/**
 * 카테고리에 등록된 상품 정보 호출 api
 *
 * @author cafe24
 * @since 2015.11.01
 * @version 1.0
 */
class apiCategoryProduct extends apiSdk
{
    public function execute()
    {

        // 넘겨받은 파라메타 설정
        $iCategoryNo = $this->args['categoryNo'];
        $iSortMethod = $this->args['sortMethod'];
        $iCount = 30;

        $aData = array();

        //카테고리 CAPI [상품정보] 호출
        $aCategory = $this->Openapi->call(
                'category',
                'product',
                array(
                       'category_no'=>$iCategoryNo,
                       'sort'=>$iSortMethod,
                       'page'=>1,
                       'limit'=>$iCount
                      )
                );

        $aCategoryList = $aCategory['meta'];
        $aCategoryList['data']['total_record']  = $aCategory['response']['total_record'];
        $aCategoryList['data']['products']  = $aCategory['response']['result'];

        // 정상적으로 데이타 받았을 경우 상태코드, 메세지, 상품정보 배열에 저장
        if ( isset($aCategoryList['data']) && count($aCategoryList['data']['products']) > 0 ) {
            $this->setStatusCode($aCategoryList['code']);
            $this->setMessage($aCategoryList['message']);
            $aData =  $aCategoryList['data'];
        }

        return $aData;
    }

}