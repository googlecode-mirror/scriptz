# Introduction #

In Oracle, you can create an autonumber field by using sequences. A sequence is an object in Oracle that is used to generate a number sequence. This can be useful when you need to create a unique number to act as a primary key.

The syntax for a sequence is:
```
    CREATE SEQUENCE sequence_name
        MINVALUE value
        MAXVALUE value
        START WITH value
        INCREMENT BY value
        CACHE value;
```
For example:
```
    CREATE SEQUENCE supplier_seq
        MINVALUE 1
        MAXVALUE 999999999999999999999999999
        START WITH 1
        INCREMENT BY 1
        CACHE 20;
```
This would create a sequence object called supplier\_seq. The first sequence number that it would use is 1 and each subsequent number would increment by 1 (ie: 2,3,4,...}. It will cache up to 20 values for performance.

If you omit the MAXVALUE option, your sequence will automatically default to:
```
    MAXVALUE 999999999999999999999999999
```
So you can simplify your CREATE SEQUENCE command as follows:
```
    CREATE SEQUENCE supplier_seq
        MINVALUE 1
        START WITH 1
        INCREMENT BY 1
        CACHE 20;
```
Now that you've created a sequence object to simulate an autonumber field, we'll cover how to retrieve a value from this sequence object. To retrieve the next value in the sequence order, you need to use nextval.

For example:
```
    supplier_seq.nextval
```
This would retrieve the next value from supplier\_seq. The nextval statement needs to be used in an SQL statement. For example:
```
    INSERT INTO suppliers
    (supplier_id, supplier_name)
    VALUES
    (supplier_seq.nextval, 'Kraft Foods');
```
This insert statement would insert a new record into the suppliers table. The supplier\_id field would be assigned the next number from the supplier\_seq sequence. The supplier\_name field would be set to Kraft Foods.


# Details #

Add your content here.  Format your content with:
  * Text in **bold** or _italic_
  * Headings, paragraphs, and lists
  * Automatic links to other wiki pages