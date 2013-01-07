
# This script:
#    - opens a .csv file containing scraped property data 
#    - converts each row to it's equivalent database insert statement
#    - writes this information to a MySQL script file that may be used to create and populate the corresponding database table 

import csv

# open the .csv file containing scraped property data
reader = csv.DictReader(open('sample.csv','rb'))

# convert the inputted data to a list
sample=list(reader)

# the name of the database table
table_name="properties"

# field_to_column_mappings maps the .csv field names to database table column names
field_to_column_mappings = {
                            "Property type":"property_type", \
                            "Address":"address", \
                            "car spaces":"car_spaces", \
                            "bathrooms":"bathrooms", \
                            "bedrooms":"bedrooms", \
                            "list price":"rent" \
                            }

# fields contains all the field names in the .csv
fields=[k for k, v in field_to_column_mappings.items()]

#columns contains all the column names in the database table
columns=[v for k, v in field_to_column_mappings.items()]
#print columns
#print " "

# column_to_type_mappings maps the database table column names to it's corresponding data type. 
# this will be useful when building the insert statement, as integer types, and NULL values, don't require quotes
column_to_type_mappings = {
                           "property_type":"VARCHAR(20)", \
                           "address":"VARCHAR(200)", \
                           "car_spaces":"VARCHAR(1)", \
                           "bathrooms":"VARCHAR(1)", \
                           "bedrooms":"VARCHAR(1)", \
                           "rent":"VARCHAR(4)" \
                           }

# open the MySQL script file 
db_script_file = open('table_script.mysql', 'w') 

# generate the sql to create the table in the database, and write it to the script file
db_script_file.write("CREATE TABLE "+table_name+" (")
db_script_file.write("id INTEGER AUTO_INCREMENT PRIMARY KEY, ")
db_script_file.write(", ".join(["%s %s" % (k, v) for k, v in column_to_type_mappings.items()]))
db_script_file.write(")ENGINE=InnoDB;\n\n")

# for each row in the sample data...
for row in sample:

    # extract field values and put them in a list. 
    values=[row[f].strip() for f in fields]
    # also, clean the data: strip whitespace, convert ??? to NULLs, convert True and False to 1 and 0, etc   
    values=["NULL" if x == "???" else x for x in values]
    values=["1" if x == "True" else x for x in values]
    values=["0" if x == "False" else x for x in values]

    insert_string=""

    # generate the insert sql...
    insert_string+="INSERT INTO "+table_name+" ("
    
    # join the column names together, separated by commas
    insert_string+=", ".join(columns)
    
    insert_string+=") VALUES\n"
    insert_string+="("
    
    # join the values together, separated by commas
    # but note, integers, booleans and NULL values don't require quotes
    for i, f in enumerate(fields):
        if column_to_type_mappings[columns[i]]=="INTEGER" or column_to_type_mappings[columns[i]]=="BOOLEAN" or values[i]=="NULL":
            insert_string+=values[i]+","
        else:            
            insert_string+="'"+values[i]+"',"
    
    # remove the trailing comma and complete the insert string
    insert_string = insert_string[:-1]
    insert_string+="); \n\n"
    
    # write to the script file
    db_script_file.write(insert_string)
    
db_script_file.close()
            
    