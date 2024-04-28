import mysql.connector


def submit_sql(filename: str):
    """ Submits the results retreived from the .sql files students submitted
    """
    global database_name
    # establish a connection to the database
    cnx = mysql.connector.connect(user='root', password='', host='localhost')
    # create a cursor object
    cursor = cnx.cursor()
    # read the contents of the .sql file
    with open(filename, 'r') as file:
        sql_script = file.read()

        # extract the database's name from the script file
        start = sql_script.find("USE ") + 4
        if not start:
            start = sql_script.find("use ") + 4
        end_string = sql_script.find("syscx;", start) + 5
        if end_string < start:
            end_string = sql_script.find("syscbook;", start) + 5

        print(sql_script[start:end_string])
        database_name = sql_script[start:end_string]

    # if database_name == "" or "firstname_lastname" in database_name.lower():
    #     return False

    # execute the script using the cursor object
    for result in cursor.execute(sql_script, multi=True):
        pass
    # commit the changes to the database
    cnx.commit()

    # close the cursor and connection
    cursor.close()
    cnx.close()
    return True


def drop_database():
    """ Drop the student's database once the grading is done
    """
    # establish a connection to the database
    cnx = mysql.connector.connect(
        user='root', database=database_name, password='', host='localhost')
    # create a cursor object
    cursor = cnx.cursor()

    # execute the script using the cursor object
    cursor.execute("DROP DATABASE " + database_name)

    # close the cursor and connection
    cursor.close()
    cnx.close()


def create_admin():
    global database_name

    # establish a connection to the database
    cnx = mysql.connector.connect(
        user='root', database=database_name, password='', host='localhost')
    # create a cursor object
    cursor = cnx.cursor()

    sql = "SELECT * FROM users_info WHERE student_email = 'admin@mail.com';"
    cursor.execute(sql)
    if len(cursor.fetchall()):
        sql = "DELETE FROM users_info WHERE student_email = 'admin@mail.com';"
        cursor.execute(sql)
        cnx.commit()

    # execute the script using the cursor object
    sql = "INSERT INTO users_info VALUES(NULL, %s, %s, %s, %s);"
    val = ("admin@mail.com", "admin", "omar", "10101010")
    cursor.execute(sql, val)
    cnx.commit()

    sql = "SELECT student_ID FROM users_info WHERE student_email = 'admin@mail.com';"
    cursor.execute(sql)
    student_ID = cursor.fetchone()[0]

    sql = "INSERT INTO users_address VALUES(" + str(
        student_ID) + ", '123', 'Admin', 'Ottawa', 'ON', 'A1A2B2');"
    cursor.execute(sql)
    cnx.commit()

    sql = "INSERT INTO users_avatar VALUES(" + str(student_ID) + ", '2');"
    cursor.execute(sql)
    cnx.commit()

    sql = "INSERT INTO users_permissions VALUES(" + str(student_ID) + ", '0');"
    cursor.execute(sql)
    cnx.commit()

    sql = "INSERT INTO users_program VALUES(" + str(
        student_ID) + ", 'Computer Systems Engineering');"
    cursor.execute(sql)
    cnx.commit()
    sql = "INSERT INTO users_passwords VALUES(" + str(
        student_ID) + ", '$2y$10$1k8Xifcv1BE4zb59fJJ.kO5xzZ9Nt.Y3PvAhrTKAyQPyWiy/LSmTu');"
    # Admin password is AdminTest
    cursor.execute(sql)
    cnx.commit()

    # close the cursor and connection
    cursor.close()
    cnx.close()


if __name__ == "__main__":

    file_name = input(
        "Enter your sql file name without the extension (without .sql): ")
    # read the sql file names
    grade = 0
    # Testing creating database and sql statements
    try:
        # retrieve the student's name from the folder name
        print("\n************ Results for ************\n")
        print(file_name, "\n")
        grade = 2
        # create the database and tables
        error_message = submit_sql(file_name + ".sql")
        message = ''
    except:
        print("Fail: ", file_name)
        message = "Comment: Error in the provided .sql file"
        grade = 0

    print("({0}/2.0) Database creation result".format(float(grade)))
    print(message)

    # Create an admin account
    create_admin()
