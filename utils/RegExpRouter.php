<?php
/**
 * @author
 * @license MIT
 */
namespace fate\utils;

class RegExpRouter {

    /**
     * 解析正则路由
     *
     * @param string $patternString 路由
     *
     * pattern: /home/{uid}         -> \/home\/(\w+)
     * pattern: /home/{uid}/{page}  -> \/home\/(\w+)\\/(\w+)
     * pattern: /home/{uid:\d+}     -> \/home\/(\d+)
     * pattern: /home/profile       -> \/home\/profile
     *
     */
    public function toRegExpRouter($patternString) {
        $parameters = null;

        // format /home/(uid)/(page:\d+)
        $pattern = str_replace(['{', '}'], ['(', ')'], $patternString);

        // replace
        if(preg_match_all('/\(\w+:?/', $pattern, $matches) > 0) {
            $parameters = [];

            // matches [ ['(uid', '(page:'] ]
            for($i=0,$len=count($matches[0]); $i<$len; $i++) {
                // () or (\d+)
                $pattern = str_replace($matches[0][$i], '(', $pattern);
                $pattern = str_replace('()', '(\w+)', $pattern);

                $matches[0][$i] = str_replace(':', '', $matches[0][$i]);

                $parameters[] = substr($matches[0][$i], 1);
            }
        }

        $pattern = trim($pattern, '/');
        $pattern = '^\\/' . str_replace('/', '\\/', $pattern) . '\\/?$';

        return [
            'pattern' => $pattern,
            'parameters' => $parameters
        ];
    }

}
