<?php
/**
 * FRONT 상품 정보
 *
 * @author cafe24
 * @since 2015.12.01
 * @version 1.0
 */
class frontDisplay extends Controller_Front
{
    protected function run($args)
    {
        // 제품 설정 Redis 정보
        $aSetup = $this->Redis->get( libConfig::APP_SETUP );

        // 이미지 업로드 Redis 정보
        $aImage = $this->Redis->get( libConfig::APP_IMAGE );

        // 캐시된 Redis 상품정보
        $aProductRows = $this->Redis->get( libConfig::APP_FRONT_PRODUCT_CACHE );

        // redis 캐시 만료 or 관리자설정저장시간과 캐시에저장된시간과 다를 경우
        if( !is_array($aProductRows) || $this->Redis->ttl( libConfig::APP_FRONT_PRODUCT_CACHE ) < 0 || $aSetup['mtime'] != $aProductRows[1] || isset($args['c']) ){

            if( isset($aSetup) && is_array($aSetup) ){

                if( $aSetup['pChoice'] === 'product' ){ // 상품선택으로 설정한 경우

                    $aProductDetail = $this->Openapi->call('product','detail', array("product_no"=>str_replace(',','|',$aSetup['p_nos'])));

                } else if( $aSetup['pChoice'] === 'category' ) {    // 카테고리로 설정한 경우

                    $sCateNo = NULL;
                    // $aSetup['depth1'] ~ $aSetup['depth4'] 카테고리 중 최종 카테고리 정보로 제품정보 호출
                    for ($i = 1 ; $i <= 4 ; $i++) {
                        if ($aSetup['depth'.$i]) {
                            $sCateNo = $aSetup['depth'.$i];

                        }
                    }

                    $aProductDetail = $this->Openapi->call('product','search', array("category_no"=>$sCateNo, "limit"=>$aSetup['rows']));
                }

                // CAPI로 호출된 정보 중 필요한 정보만 배열에 저장
                $aProductRows[0] = $this->setProductItem( $aProductDetail['response']['result'] );
                $aProductRows[1] = $aSetup['mtime'];

                $this->Redis->set( libConfig::APP_FRONT_PRODUCT_CACHE, $aProductRows );
                $this->Redis->expire(libConfig::APP_FRONT_PRODUCT_CACHE, libConfig::EXPIRE_TIME );

            }else{

                // 관리자 설정 정보가 없는경우 등록일 기준으로 3개 상품 출력
                $aProductDetail = $this->Openapi->call('product','search', array("limit"=>3,"sort"=>4));
                $aProductRows[0] = $this->setProductItem( $aProductDetail['response']['result'] );
            }
        }

        // 업로드 이미지 경로 문자열 생성
        if ( is_array($aImage) && !empty($aImage) ){
            $sImgUrl = '<img src="'.libUtil::pFilePath($aImage['m_upload_path'].$aImage['m_upload_name']).'">';
        }

        // 스마트 디자인 기반 Front 출력 변수 할당
        $this->assign('image_url',$sImgUrl);    // 이미지 경로
        $this->loopFetch( $aProductRows[0] );  // 상품정보 배열

        // HTTP 상태 코드 설정
        $this->setStatusCode('200');
    }

    /**
     * CAPI 호출된 정보 배열에 저장
     * @param array $aProductDetail CAPI호출된배열정보
     * @return array $aResult 출력될기본정보배열
     */
    private function setProductItem($aProductDetail)
    {

        if( is_array($aProductDetail) ){

            foreach( $aProductDetail as $key=>$val ){
                $aResult[$key]['prd_name'] = $val['prd_name'];
                $aResult[$key]['prd_img'] = $val['prd_img_small_url'];
                $aResult[$key]['prd_price'] = number_format((int)$val['prd_price']);
                $aResult[$key]['prd_detail_link'] = 'http://'.$this->Request->getDomain().'.cafe24.com'.$val['prd_detail_link'];

            }
        }

        return $aResult;
    }

}
