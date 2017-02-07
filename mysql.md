# MySQL 开发规范

## 命名规范

* 库名、表名、字段名必须使用小写字母，并采用下划线分割。
* 库名、表名、字段名禁止超过32个字符。
* 库名、表名、字段名见名知意，使用名词而不是动词。
* 库名、表名、字段名，使用常见单词，避免使用长单词和生僻词。
* 表名、字段名应当有注释，描述该表、字段的用途。

## 表字段设计规范

* 使用InnoDB存储引擎，表字符集选择UTF8。
* 使用UNSIGNED存储非负数值。
* 使用INT UNSIGNED存储IPV4。
* 用DECIMAL代替FLOAT和DOUBLE存储精确浮点数。
* INT类型固定占4字节存储，例如INT(4)仅代表显示字符宽度为4位，不代表存储长度。
* 区分使用TINYINT、SMALLINT、MEDIUMINT、INT、BIGINT数据类型。例如取值范围为0-80时，使用TINYINT UNSIGNED。
* 建议字段定义为NOT NULL。
* 尽可能不使用TEXT、BLOB类型。
* VARCHAR(N)，N表示的是字符数不是字节数，比如VARCHAR(255)，可以最大可存储255个汉字，需要根据实际的宽度来选择N。
* VARCHAR(N)，N尽可能小，因为MySQL一个表中所有的VARCHAR字段最大长度是65535个字节，进行排序和创建临时表一类的内存操作时，会使用N的长度申请内存。
* 时间字段，除特殊情况一律采用int来记录时间戳；使用`created_time`表示记录创建时间、`updated_time`表示记录更新时间。
* 禁止在数据库中存储图片、文件等大数据。

## 索引

* 表的主键一般都约定成为id，自增类型。
* 索引中的字段数建议不超过5个，单张表的索引数量控制在5个以内。
* 索引名称必须使用小写。
* 非唯一索引必须按照“idx_字段名_字段名[_字段名]”进行命名，唯一索引必须按照“uniq_字段名_字段名[_字段名]”进行命名。
* 组合索引建议包含所有字段名，过长的字段名可以采⽤缩写形式。
* 禁止冗余索引、禁止重复索引。
* 索引字段的顺序需要考虑字段值去重之后的个数，个数多的放在前面。
* ORDER BY，GROUP BY，DISTINCT的字段需要添加在索引的后面。
* 使用EXPLAIN判断SQL语句是否合理使用索引，尽量避免extra列出现：Using File Sort，Using Temporary。
* UPDATE、DELETE语句需要根据WHERE条件添加索引。
* 对长度过长的VARCHAR字段建立索引时，添加crc32或者MD5 Hash字段，对Hash字段建立索引。
* 合理创建联合索引（避免冗余），(a,b,c) 相当于 (a) 、(a,b) 、(a,b,c)。
* 合理使用覆盖索引减少IO，避免排序。

## SQL

* 使用prepared statement，可以提供性能并且避免SQL注入。
* 避免在SQL语句进行数学运算或者函数运算。
* SQL语句中IN包含的值不应超过1000个。
* 统计表中记录数时使用COUNT(*)，而不是COUNT(primary_key)。
* 避免对记录行数超过1000行的表使用`%前导查询`，因为`%前导查询`无法利用到索引。
* 避免使用JOIN和子查询。必要时推荐用JOIN代替子查询。
* 不使用ORDER BY RAND()，使用其他方法替换。
* 避免使用含业务逻辑的存储过程、触发器、函数等。
* WHERE条件中必须使用合适的类型，避免MySQL进行隐式类型转化。

## 参考文献

  * http://www.imcjd.com/?p=1237
  * http://imysql.com/2015/07/23/something-important-about-mysql-design-reference.shtml
  * http://highdb.com/mysql%E5%BC%80%E5%8F%91%E8%A7%84%E8%8C%83/