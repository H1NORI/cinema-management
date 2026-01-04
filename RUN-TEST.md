Before running any tests, update DB configs to use cinema_management_test, you should change it here: 

```
www/yii-project/api/config/test.php
```
and 
```
www/yii-project/common/config/main-local.php
```
---

build tests config for API:
```
vendor/bin/codecept build -c api
```
---

run specific test group:
```
vendor/bin/codecept run -c api functional UserCest -v

vendor/bin/codecept run -c api functional ProgramCest -v

vendor/bin/codecept run -c api functional ScreeningCest -v
```
---