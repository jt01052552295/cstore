<?php
/**
 * 설정 정보 상수 정의
 *
 * @author cafe24
 * @since 2015.11.01
 * @version 1.0
 */
class libConfig
{
    // 캐시 만료 시간 (단위 : 초)
    const EXPIRE_TIME = 86400;

    // Redis 네임스페이스
    const APP_NAMESPACE = "githubsample";
    // 사용 설정 정보
    const APP_SETUP = "setup";
    // 업로드 이미지 정보
    const APP_IMAGE = "image";
    // 카테고리 정보(캐시 처리)
    const APP_CATEGORY_CACHE = "category_cache";
    // Front 상품 정보(관리자에서 상품정보 수정안할 경우 24시간 캐시)
    const APP_FRONT_PRODUCT_CACHE = "front_product_cache";
}
