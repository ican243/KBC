<?php

/**
 * 입력값 검사 오류 문구 (한국어)
 * {field}: 항목 이름, {param}: 규칙 값
 */
return [
    'required'    => '[{field}] 항목을 입력해 주세요.',
    'min_length'  => '[{field}] 항목은 {param}자 이상이어야 합니다.',
    'max_length'  => '[{field}] 항목은 {param}자 이내로 입력해 주세요.',
    'alpha_dash'  => '[{field}] 항목에는 영문, 숫자, 밑줄(_), 하이픈(-)만 쓸 수 있습니다.',
    'is_unique'   => '[{field}] 값이 이미 사용 중입니다. 다른 값을 입력해 주세요.',
    'integer'     => '[{field}] 항목은 숫자로 입력해 주세요.',
    'valid_date'  => '[{field}] 항목의 날짜 형식이 올바르지 않습니다.',
    'valid_email' => '[{field}] 항목의 이메일 형식이 올바르지 않습니다.',
    'matches'     => '[{field}] 항목이 [{param}] 항목과 일치하지 않습니다.',
    'in_list'     => '[{field}] 항목의 값이 올바르지 않습니다.',
];
