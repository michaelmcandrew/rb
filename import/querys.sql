SELECT col_4, count(*), group_concat(col_18) # there is more than one row that shares the same name and none have a yes
FROM `contacts`
WHERE col_4 !='' 
GROUP BY col_4
HAVING group_concat(col_18) NOT LIKE '%yes%' AND count(*) > 1

SELECT col_4, count(*), group_concat(col_18) # these rows should have a yes
FROM `contacts`
WHERE col_4 !='' 
GROUP BY col_4
HAVING group_concat(col_18) NOT LIKE '%yes%' AND count(*) = 1

SELECT col_4, count(*), group_concat(col_18) # these rows have more than one yes - they should only have one
FROM `contacts`
WHERE col_4 !='' 
GROUP BY col_4
HAVING group_concat(col_18) LIKE '%yes%yes%' AND count(*) > 1