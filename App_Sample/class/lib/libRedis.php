<?php
/**
 * Redis 를 이용하여, 데이터베이스 처럼 사용하기 위한 클래스
 *
 * @author cafe24
 * @since 2015.11.01
 * @version 1.0
 */
class libRedis
{
    public static $instance;

    /**
     * libRedis 인스터스 얻기
     */
    public static function instance( $redis, $namespace )
    {
        if ( !isset( $redis ) ) {
            return null;
        }
        self::$instance = ( isset( self::$instance ) ) ? self::$instance : new libRedis ( $redis, $namespace ) ;
        return self::$instance;
    }

    /**
     * 셋팅된 Redis 인스턴스
     * @var object
     */
    private $_redis;

    /**
     * 네임 스페이스 지정
     * @var string
     */
    private $_namespace;

    /**
     * 마지막에 삽입된 pk
     */
    private $_lastPk;

    /**
     * 레디스 , 네임스페이스 setting
     *
     * @param object $redis
     * @param String $namespace
     */
    public function __construct( $redis, $namespace = "" )
    {
        $this->_redis = $redis;
        $this->_namespace = $namespace;
    }

    /**
     * 식별키 생성하기 INCR
     */
    public function getPk( $pk )
    {
        return $this->_redis->incr( $this->setNameSpace($pk) . ":pk");
    }

    /**
     * redis 데이터 인서트 하기
     */
    public function insert( $key, $aData )
    {
        $aData['seq'] = $this->_lastPk = $this->getPk($key);
        $value = $this->pack_value($aData);
        $result = $this->_redis->rPush($this->setNameSpace($key), $value);
        return $result;
    }

    /**
     * 인서트시 마지막 생성된 pk 값 추출
     */
    public function get_last_pk()
    {
        return $this->_lastPk;
    }


    /**
     * 먼처음 행 얻기 config, 행 할 때 사용
     * @return array
     */
    public function get_first_row( $key )
    {
        return $this->getIndex($key, 0);
    }

    /**
     * 키값에 네임스페이스 부여하기
     *
     * @param $key String
     * @return String
     */
    private function setNameSpace($key)
    {
        return $this->_namespace . ":" . $key;
    }

    /**
     * 리스트 가지고오기 페이징 리스트 처리할때 사용
     * @return array
     */
    public function get_list( $key, $limit, $offset = 0 )
    {
        $limit--;
        $start = $offset;
        $end = $start + $limit;

        $response = $this->_redis->lRange( $this->setNameSpace($key) , $start, $end );

        $count = count($response);
        $list = array();

        for ( $i = 0; $i < $count; $i++ ) {
            $list[] = $this->unpack_value($response[$i]);
        }

        return $list;
    }


    /**
     * 리스트 순서 거꾸로 가지고 오기 _desc
     * @return array
     */
    public function get_list_desc( $key, $rows = 10, $page = 1, $totalCount = 0  )
    {
        $start =  $totalCount - ( $page * $rows ) ;
        $end = $start + ( $rows  - 1 );
        $start = ( $start < 0 ) ? 0 : $start;

        $response = $this->_redis->lRange( $this->setNameSpace($key) , $start, $end );

        $count = count($response);
        $list = array();

        for ( $i = 0; $i < $count; $i++ ) {
            $list[] = $this->unpack_value($response[$i]);
        }

        $list = is_array( $list ) ? array_reverse( $list ) : $list;
        return $list;
    }

    /**
     * 마지막 건수부터 얻어오기
     * @param string $key
     * @param 뒷번호 시작 $iStart
     * @param 가져올 갯수 $iLimit
     */
    public function lastLimit( $key, $iStart , $iLimit = 10 )
    {
        $iStart = "-" . $iStart;
        $response = $this->_redis->lRange( $this->setNameSpace($key) , $iStart, $iLimit );

        $count = count($response);
        $list = array();

        for ( $i = 0; $i < $count; $i++ ) {
            $list[] = $this->unpack_value($response[$i]);
        }

        return $list;
    }


    /**
     * 전체 리스트 목록 얻기
     */
    public function get_all_list( $key )
    {
        $end = $this->get_list_length( $key );
        return $this->get_list( $key, $end );
    }

    /**
     * 해당 인덱스 에 필드 값 얻기
     */
    public function getIndex($key, $index)
    {
        $response = $this->_redis->lGet( $this->setNameSpace($key) , $index );
        return $this->unpack_value($response);
    }

    /*
    * 전체 결과를 얻은후 검색 결과에 따라 데이터 반환하기
    */
    public function getSearch( $key, array $filters )
    {
        $aData = array();
        $lists = $this->get_all_list( $key );
        foreach ( $lists  as $v ) {
            foreach ( $filters as $k => $f ) {
                if ( $v[$k] == $f ) {
                    $aData[] = $v;
                    break;
                }
            }
        };
        return $aData;
    }

    /**
     * 필터 값에 따른 리스트 배열 얻어오기, 검색 시 사용
     *
     * @param $key 키
     * @param $filters array 검색할 값
     */
    public function get_filtered_list( $key, array $filters, $limit = 0, $offset = 0 )
    {
        $start = $added = 0;
        $end = $this->get_list_length( $key );

        $response = $this->_redis->lRange( $this->setNameSpace($key), $start, $end );

        $limit = !$limit ? $end : $limit + $offset;
        $list = array();

        for ( $i = 0; $i < $end; $i++ ) {
            $value = $this->unpack_value( $response[$i] );
            if ( is_array($value) ) {
                $sect = array_intersect($value, $filters);
                if ( $filters['seq'] ==  $sect['seq'] ) {
                        $list[$i] = $value;
                        break;
                }
                if ( ( $filters ==  $sect ) && ( ++$added <= $limit ) ) {
                    $list[$i] = $value;
                }
            }
        }
        return $list;
    }

    /**
    * 해당 조건 찿아서 값 지우기
    *
    * @return bool
    */
    public function remove_by_filter( $key, $filters )
    {
        $list = $this->get_filtered_list( $key, $filters );

        if ( count($list) > 0 ) {
            foreach ( $list as $item ) {
                $result = $this->remove_from_list($key, $item);
            }
        }
        return $result;
    }

    /**
    * 값 제거하기
    */
    public function remove_from_list( $key, $value, $count =0 )
    {
        $value = $this->pack_value($value);
        $response = $this->_redis->lRem( $this->setNameSpace($key), $value, $count );
        return $response;
    }

    /**
     * 업데이트 하기
     */
    public function update( $key, $index, $aData )
    {
        $value = $this->pack_value($aData);
        $response = $this->_redis->lSet( $this->setNameSpace($key), $index, $value );
        return $response;
    }

    /**
     * 라스트에 등록됀 행 가지고오기
     */
    public function get_last_row($key)
    {
        $response = $this->_redis->getIndex($key, -1);
        return $response;
    }

    /**
     * 첫번째 로우 반환하기
     */
    public function first_pop( $key)
    {
        $response = $this->_redis->lPop( $this->setNameSpace($key) );
        return $response;
    }

    public function last_pop( $key )
    {
        $response = $this->_redis->rPop( $this->setNameSpace($key) );
        return $response;
    }

    /**
     * 리스트의 전체 사이즈 얻기
     */
    public function get_list_length( $key )
    {
        $result = $this->_redis->lLen( $this->setNameSpace($key) );
        return $result;
    }

    /**
     * 해당 키값의 전체사이즈 얻기
     */
    public function get_size( $key )
    {
        $response = $this->_redis->lSize( $this->setNameSpace($key) );
        return $reponse;
    }

    /**
     * serialize 압축하기.
     */
    private function pack_value( $value )
    {
        if ( is_numeric($value) ) {
            return $value;
        } else {
            return serialize($value);
        }
    }

    /**
     * unserialize 압축해지하기
     */
    private function unpack_value( $packed )
    {
        if ( is_numeric($packed) ) {
            return $packed;
        }
        return unserialize($packed);
    }

    /**
     * 에러 메시지 반화
     */
    private function get_error( $response )
    {
        if ( strpos($response, '-ERR') === 0 ) {
            return substr($response, 5);
        }
        return false;
    }
}