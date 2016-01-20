# Oracle Alter Table Howto #

The ALTER TABLE command allows you to add, modify, or drop a column from an existing table.

## Adding column(s) to a table ##

Syntax #1

To add a column to an existing table, the ALTER TABLE syntax is:
```
    ALTER TABLE table_name
     ADD column_name column-definition;
```
For example:
```
    ALTER TABLE supplier
     ADD supplier_name  varchar2(50);
```
This will add a column called supplier\_name to the supplier table.

Syntax #2

To add multiple columns to an existing table, the ALTER TABLE syntax is:
```
    ALTER TABLE table_name
    ADD ( 	column_1 	column-definition,
    	column_2 	column-definition,
    	... 	
    	column_n 	column_definition );
```
For example:
```
    ALTER TABLE supplier
    ADD ( 	supplier_name 	varchar2(50),
    	city 	varchar2(45) );
```
This will add two columns (supplier\_name and city) to the supplier table.

## Modifying column(s) in a table ##

Syntax #1

To modify a column in an existing table, the ALTER TABLE syntax is:
```
    ALTER TABLE table_name
     MODIFY column_name column_type;
```
For example:
```
    ALTER TABLE supplier
     MODIFY supplier_name   varchar2(100)     not null;
```
This will modify the column called supplier\_name to be a data type of varchar2(100) and force the column to not allow null values.

Syntax #2

To modify multiple columns in an existing table, the ALTER TABLE syntax is:
```
    ALTER TABLE table_name
    MODIFY ( 	column_1 	column_type,
    	column_2 	column_type,
    	... 	
    	column_n 	column_type );
```
For example:
```
    ALTER TABLE supplier
    MODIFY ( 	supplier_name 	varchar2(100) 	not null,
    	city 	varchar2(75) 	  	);
```
This will modify both the supplier\_name and city columns.

## Drop column(s) in a table ##

Syntax #1

To drop a column in an existing table, the ALTER TABLE syntax is:
```
    ALTER TABLE table_name
     DROP COLUMN column_name;
```
For example:
```
    ALTER TABLE supplier
     DROP COLUMN supplier_name;
```
This will drop the column called supplier\_name from the table called supplier.

## Rename column(s) in a table ##
(NEW in Oracle 9i Release 2)

Syntax #1

Starting in Oracle 9i Release 2, you can now rename a column.

To rename a column in an existing table, the ALTER TABLE syntax is:
```
    ALTER TABLE table_name
     RENAME COLUMN old_name to new_name;
```
For example:
```
    ALTER TABLE supplier
     RENAME COLUMN supplier_name to sname;
```
This will rename the column called supplier\_name to sname.