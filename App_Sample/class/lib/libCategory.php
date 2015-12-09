<?php
/**
 * 카테고리 데이터 tree 구조로 만들어서 반환하기
 *
 * @author cafe24
 * @since 2015.11.01
 * @version 1.0
 */
class libCategory
{
    /**
     * 카테고리 배열을 트리구조 카테고리 배열로 변경
     *
     * @param array $aCategoryList api로호출된 카테코리 배열
     */
    static public function tree( $aCategoryList )
    {
        $aCategoryData = array();

        if ( $aCategoryList && $aCategoryList['code']==200 ) {
            foreach ($aCategoryList['data'] as $No => $Cateogory) {
                $childData = array();
                if ( count($Cateogory['cat_childs']) > 0 ) {
                    self::_categoryChildData($childData, $Cateogory['cat_childs']);
                }
                $aCategoryData[$No] = array (
                    'categoryNo'=>$Cateogory['cat_no'],
                    'categoryName'=>$Cateogory['cat_name'],
                    'childData'=>$childData
                );
            }
        }
        return $aCategoryData;
    }

    /**
     * 하위 카테고리 배열 저장(재귀함수)
     *
     * @param array $Data
     * @param array $aChildData
     */
    static private function _categoryChildData( &$Data, $aChildData )
    {
        $childData = array();
        foreach($aChildData as $No=>$Cateogory) {
            if ( count($Cateogory['cat_childs']) > 0  ) {
                self::_categoryChildData($childData, $Cateogory['cat_childs']);
            }
            $Data[$No] = array
            (
                'categoryNo'=>$Cateogory['cat_no'],
                'categoryName'=>$Cateogory['cat_name'],
                'childData'=>$childData
            );
        }
    }
}
