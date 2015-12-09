<?php
/**
 * 여러가지 유틸정보 처리
 *
 * @author cafe24
 * @since 2015.11.01
 * @version 1.0
 */
class libUtil
{
    static public function checkImgSize( $sWidth, $sHeight )
    {
        $sWidth = ( empty( $sWidth ) ) ? "600" : $sWidth;
        $sHeight = ( empty( $sHeight ) ) ? "460" : $sHeight;
        $sWidth = ( $sWidth < 450 ) ? 450 : $sWidth;
        $sHeight = ( $sHeight < 310 ) ? 310 : $sHeight;
        return array( $sWidth, $sHeight );
    }

    static public function unsetData( $aData )
    {
        unset( $aData['FILE_UPLOAD_INSTANCE'] );
        unset( $aData['act'] );
        unset( $aData['products'] );
        foreach ( $aData as $k => $v ) {
            if ( empty( $v ) ) {
                unset( $aData[$k] );
            }
        }
        return $aData;
    }

    /**
     * 텍스트 특정 문자열 자르기
     *
     * @param string $sTitle
     * @param number $sLen
     * @param boolean $bTag
     */
    static public function cutString( $sTitle, $iLen = 40, $bTag = false )
    {
        $sTitle = strip_tags( $sTitle );
        if ( mb_strlen($sTitle) > $iLen ) {
            mb_internal_encoding('UTF-8');
            $sNewString = mb_strimwidth($sTitle, 0, $iLen, '');
            return mb_substr($sNewString, 0, mb_strlen($sNewString)) .  "...";
        }
        return $sTitle;
    }

    /**
     * 배열의 값을 랜덤하게 생성한후 반환하기
     * @param array $aData
     * @param array $iRow
     */
    static public function randValue( $aData, $iRow )
    {
        $iKeys = array_keys($aData);
        shuffle($iKeys);
        $aResult = array();
        for ($i = 0; $i < $iRow; $i++) {
            $aResult[] = $aData[$iKeys[$i]];
        }
        return $aResult;
    }

    /**
     * 이미지 체크 하기.
     * @param string $sImg
     */
    static public function imgCheck( $sImg )
    {
        //return  preg_match("#\.gif|\.png|\.jpg|\.jpeg|\.bmp#", $sImg);
        return  preg_match('/(\.gif|\.png|\.jpg|\.jpeg|\.bmp)$/i', $sImg);
    }

    /**
     * 날짜 분리하고 기호 달기
     * @param string $sDate
     * @param string $sSplit
     */
    static public function dateSplit( $sDate, $sSplit = "-")
    {
        if ( strlen( $sDate) == 8 ) {
            return substr($sDate, 0, 4) . $sSplit . substr($sDate, 4, 2)  . $sSplit .  substr($sDate, 6, 2);
        }

        return $sDate;
    }

    /**
     * 스토리지에 저장된 파일명 서비스 경로로 치환
     *
     * @param strong $sPath 파일 경로(파일명)
     */
    static public function pFilePath( $sPath )
    {
        return '[pfile='.str_replace('public_files/','',$sPath).']';
    }
}
