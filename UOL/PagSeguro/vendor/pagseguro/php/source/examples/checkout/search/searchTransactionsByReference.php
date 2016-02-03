<?php
/*
 ************************************************************************
 Copyright [2014] [PagSeguro Internet Ltda.]

 Licensed under the Apache License, Version 2.0 (the "License");
 you may not use this file except in compliance with the License.
 You may obtain a copy of the License at

 http://www.apache.org/licenses/LICENSE-2.0

 Unless required by applicable law or agreed to in writing, software
 distributed under the License is distributed on an "AS IS" BASIS,
 WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 See the License for the specific language governing permissions and
 limitations under the License.
 ************************************************************************
 */

require_once "../../../PagSeguroLibrary/PagSeguroLibrary.php";

class SearchTransactionsByReference
{

    public static function main()
    {

        $reference = "REF123";

        $initialDate = '2015-09-03T00:00';
        $finalDate = '2015-09-09T11:13';
        $pageNumber = 1;
        $maxPageResults = 20;
        
//        $reference = "REF123";
//
//        $initialDate = '2014-12-03T00:00';
//        $finalDate = '2014-12-08T00:00';
//        $pageNumber = 1;
//        $maxPageResults = 20;

        try {

            /*
             * #### Credentials #####
             * Substitute the parameters below with your credentials
             * You can also get your credentials from a config file. See an example:
             * $credentials = PagSeguroConfig::getAccountCredentials();
             */
            // seller authentication
            $credentials = new PagSeguroAccountCredentials("vendedor@lojamodelo.com.br",
                "E231B2C9BCC8474DA2E260B6C8CF60D3");
            
            $credentials = PagSeguroConfig::getAccountCredentials();

            // application authentication
            //$credentials = PagSeguroConfig::getApplicationCredentials();

            //$credentials->setAuthorizationCode("E231B2C9BCC8474DA2E260B6C8CF60D3");

            $result = PagSeguroTransactionSearchService::searchByReference(
                $credentials,
                $reference,
                $initialDate,
                $finalDate,
                $pageNumber,
                $maxPageResults
            );

            self::printResult($result, $initialDate, $finalDate);

        } catch (PagSeguroServiceException $e) {
            die($e->getMessage());
        }

    }

    public static function printResult(PagSeguroTransactionSearchResult $result, $initialDate, $finalDate)
    {
        $finalDate = $finalDate ? $finalDate : 'now';
        echo "<h2>Search transactions by Reference</h2>";
        echo "<h3>$initialDate to $finalDate</h3>";
        $transactions = $result->getTransactions();
        if (is_array($transactions) && count($transactions) > 0) {
            foreach ($transactions as $key => $transactionSummary) {
                echo "Code: " . $transactionSummary->getCode() . "<br>";
                echo "Reference: " . $transactionSummary->getReference() . "<br>";
                echo "amount: " . $transactionSummary->getGrossAmount() . "<br>";
                echo "<hr>";
            }
        }
    }
}

SearchTransactionsByReference::main();