<?php
/**
 * Restful API 호출 기본 추상 클래스
 *
 * @author cafe24
 * @since 2015.11.01
 * @version 1.0
 */
abstract class apiSdk extends Controller_Api
{

    public function get( $args)
    {
        return $this->prepare( $args );
    }

    public function post( $args )
    {
        return $this->prepare( $args );
    }

    public function put($args)
    {
        return $this->prepare( $args );
    }

    public function delete($args)
    {
        return $this->prepare( $args );
    }

    /**
     * 공통으로 사용하는 인자값 배열을 가진다.
     *
     * @var array
     */
    public $args = NULL;

    /**
     * 넘겨받은 파라메타 저장 후 실행
     *
     * @param array $aArgs 파라메타
     * @return array $aData 실행후 받은 결과 데이타
     */
    private function prepare( $aArgs )
    {
        $this->args = $aArgs;
        $aData = $this->execute();
        return $aData;
    }

    /**
     * 실행 추상 함수
     */
    abstract function execute();
}