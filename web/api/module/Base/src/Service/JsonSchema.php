<?php
/**
 *
 *
 * @author    sfs teams <zfsfs.team@gmail.com>
 * @copyright 2010-2018 (http://www.sfs.tw)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://www.sfs.tw
 * Date: 2018/8/19
 * Time: 下午 3:31
 */

namespace Base\Service;

class JsonSchema
{
    public function __construct($container)
    {
        return $this;
    }

    public function check($data, $file)
    {
        $retriever = new \JsonSchema\Uri\UriRetriever;
        $schema = $retriever->retrieve('file://'.getcwd().'/public/json_schema/'.$file);
        $validator = new \JsonSchema\Validator();
        $validator->check($data, $schema);

        if ($validator->isValid()) {
            $message = '';
        } else {
            $message =  "錯誤!! JSON 資料檢查有誤. 檢查如下:\n";
            foreach ($validator->getErrors() as $error) {
                if ($error['constraint'] == 'enum')
                    $message .= sprintf("[%s] %s\n", $error['property'], 'Does not have a value in the enumeration ["'.implode('","',$error['enum']).'"]'."\n");
                else
                    $message .= sprintf("[%s] %s\n", $error['property'], $error['message']);

            }
        }
        return $message;
    }
}