import tkinter as tk
from customtkinter import *

from time import sleep
from tqdm import tqdm
from numpy import *

import pandas as pd
 
import mysql.connector as mysql



## Creation of the main window
class Setup(CTk):
    def __init__(self, *args, **kwargs):
        ## initialize basics settings of the main window, like the bg color, the size of the window ect.
        super().__init__(*args, **kwargs)
        self._iconify_called_before_window_exists =False
        self.geometry("300x350")  ## 400px hor & 500px ver
        self.configure(bg="#efefef")  ## bg color
        #self.iconbitmap("assets/CAP-icon.ico")
        self.title("    Setup database")  ## Title of the window
        self.resizable(False, False)  ## Can't resize the window on abs and ord coordinates

        weeknumlabel = CTkLabel(self,width=140,height=30,text="Quelle semaine ?")
        weeknumlabel.place(relx=0.5,rely=0.1, anchor=E)

        self.weeknumEntry = CTkEntry(self, placeholder_text="16") 
        self.weeknumEntry.place(relx=0.5,rely=0.1, anchor= W)

        ## Creating the button to import the excel we want to setup in database
        warwait = CTkButton(master=self,  ## Button will be on our window
                            width= 140,  
                            height= 30,
                            text="Import Warwait",
                            corner_radius=5,  ## Rounding corner by 5px
                            border_width=1,  ## Litle border_width of 2px
                            border_color="#0070ad",
                            fg_color="#13abdb",
                            hover_color="#0070ad",
                            text_color="#efefef",
                            command=self.warwait, ## on click, button wil execute the importExcel method of the Setup class 
                            anchor="NW",  ## Button will consider the origin coordinates ([0,0]), in NorthWest (like conventionals programs)
                            )
        warwait.place(relx=0.5, rely=0.3, anchor=CENTER)  ## place the button in the center for the x and a litle closer to the top for the y. Anchor center means this is tied to the center of the button (if we put NW here, the button wouldnt be in the center). 


        matrice = CTkButton(master=self,  ## Button will be on our window
                            width= 140,  
                            height= 30,
                            text="Import Skills matrix",
                            corner_radius=5,  ## Rounding corner by 5px
                            border_width=1,  ## Litle border_width of 2px
                            border_color="#0070ad",
                            fg_color="#13abdb",
                            hover_color="#0070ad",
                            text_color="#efefef",
                            command=self.skillsMatrix, ## on click, button wil execute the importExcel method of the Setup class 
                            anchor="NW",  ## Button will consider the origin coordinates ([0,0]), in NorthWest (like conventionals programs)
                            )
        matrice.place(relx=0.5, rely=0.5, anchor=CENTER)  ## place the button in the center for the x and a litle closer to the top for the y. Anchor center means this is tied to the center of the button (if we put NW here, the button wouldnt be in the center). 


        quit = CTkButton(master=self,  ## Button will be on our window
                            width= 150,  
                            height= 35,
                            text="Quit",
                            corner_radius=5,  ## Rounding corner by 5px
                            border_width=2,  ## Litle border_width of 2px
                            border_color="#0070ad",
                            fg_color="#078bd4",
                            hover_color="#0070ad",
                            text_color="#efefef",
                            command=self.quit, ## on click, button wil execute the importExcel method of the Setup class 
                            anchor="NW",  ## Button will consider the origin coordinates ([0,0]), in NorthWest (like conventionals programs)
                            )
        quit.place(relx=0.5, rely=0.7, anchor=CENTER)  ## place the button in the center for the x and a litle closer to the top for the y. Anchor center means this is tied to the center of the button (if we put NW here, the button wouldnt be in the center). 


        ######## Database constants
        self.HOST = "localhost"       ## replace by the domain or the ip address

        self.DATABASE = ["cis_nord_warwait","skillmatrix"]  ## replace by the name of the database

        self.USER = "root"          ## replace by a user administrator of the db

        self.PASSWORD = "#Admin31415"  ## replace by the password tied with the user

    def quit(self) :
        self.destroy()

    def warwait(self):
        ## Warning messagebox, if the user continues, the former database will be deleted, this application is only used to configure a new db with a new excel.
        ok = tk.messagebox.askokcancel(title="Warning", message="If you continue, the former database'll be deleted. \nKeep going ?")
        if ok:  
            ## if user pressed ok, let him chose the excel he want, and save absolute path to filename
            filename = filedialog.askopenfilename()
            if filename == "":
                pass
            elif filename[-5:] == ".xlsx": ## verify if user openned an excel file
                ## now, let the readExcel collect everything inside the excel into a dictionnary, then it'll be easier to put it into database. 
                self.readExcel(filename,"ww")
            else:
                tk.messagebox.showerror(title="Error", message="You can't open a non-excel file\nrequire '.xlsx' extension.")

    def skillsMatrix(self):
        ## Warning messagebox, if the user continues, the former database will be deleted, this application is only used to configure a new db with a new excel.
        ok = tk.messagebox.askokcancel(title="Warning", message="If you continue, the former database'll be deleted. \nKeep going ?")
        if ok:  
            ## if user pressed ok, let him chose the excel he want, and save absolute path to filename
            try:
                filename = filedialog.askopenfilename()
                if filename == "":
                    pass
                elif filename[-5:] == ".xlsx": ## verify if user openned an excel file
                    ## now, let the readExcel collect everything inside the excel into a dictionnary, then it'll be easier to put it into database. 
                    self.readExcel(filename,"sm")
                else:
                    tk.messagebox.showerror(title="Error", message="You can't open a non-excel file\nrequire '.xlsx' extension.")
            except PermissionError as e:
                tk.messagebox.showerror(title="Error", message="You can't do it if the excel file is already running.")

    def readExcel(self, path_to_excel,_type):
        # This method is now useless im gonna delete it
        self.fillDB(path_to_excel,_type)

    def fillDB(self,path_to_excel,_type):

        #///////////////////////////////////////////////////////////////// La warwait
        if _type == "ww":
            try:
                numweek = int(self.weeknumEntry.get())
            except ValueError as ve:
                tk.messagebox.showerror(title="Error", message="Enter an integer for the week's num please.")
            except Exception as e:
                tk.messagebox.showerror(title="Error", message="Error when converting the week's num, please try again.")

            try:
                database = mysql.connect(host=self.HOST,
                                        database=self.DATABASE[0],
                                        user=self.USER,
                                        password=self.PASSWORD)
                print("Connected to database... OK")
            except mysql.Error as e:
                raise Exception("Cant connect to the database : {}".format(e))
            cursor = database.cursor()
            try:
                week = pd.read_excel(path_to_excel, sheet_name=numweek, skiprows=8, nrows=1)

                db = pd.read_excel(path_to_excel, sheet_name=17, skiprows=9) ## reading the excel file, stock it into a 'pandas.core.frame.DataFrame' (itterable object)
            except PermissionError as e:
                tk.messagebox.showerror(title="Error", message="You can't do it if the excel file is already running.")
                raise PermissionError("Cant open the file, it is already running.")
            lis = db.to_numpy()
            week_list = week.to_numpy()

            create_table_warwait = """ CREATE TABLE IF NOT EXISTS warwait (nom TEXT, grade TEXT, site TEXT,s1 TEXT,s2 TEXT,s3 TEXT,s4 TEXT,s5 TEXT,s6 TEXT,s7 TEXT,s8 TEXT,s9 TEXT,s10 TEXT,s11 TEXT,s12 TEXT,s13 TEXT,s14 TEXT,s15 TEXT,s16 TEXT,s17 TEXT,s18 TEXT,s19 TEXT,s20 TEXT,s21 TEXT,s22 TEXT,s23 TEXT,s24 TEXT,s25 TEXT,s26 TEXT,s27 TEXT,s28 TEXT,s29 TEXT,s30 TEXT,s31 TEXT,s32 TEXT,s33 TEXT,s34 TEXT,s35 TEXT,s36 TEXT,s37 TEXT,s38 TEXT,s39 TEXT,s40 TEXT,s41 TEXT,s42 TEXT,s43 TEXT,s44 TEXT,s45 TEXT,s46 TEXT,s47 TEXT,s48 TEXT,s49 TEXT,s50 TEXT,s51 TEXT,s52 TEXT,reussite FLOAT,positionnement TEXT,competences TEXT,cv_code INTEGER,pe TEXT,en_mission TEXT) """
            try:
                cursor.execute("DROP TABLE IF EXISTS warwait")  # Delete former table
                cursor.execute(create_table_warwait)                    # Create the new table empty
                print("Removed former and create new table... OK")

            except mysql.Error as e:  # printing reason of error if it happen
                raise Exception("Cant create the table : {}".format(e))

            database.commit() 
            
            # taking weeks number on warwait and throwing away all empty cells in the list
            week_list = week_list[0]
            week_list = week_list[~isnan(week_list)]
            # transform float to int
            week_list = week_list.astype(int)
            for ligne in tqdm(lis):
                ## getting all informations from the line
                ## Ugly code here, need to find a better and more elegant way
                sleep(0.1)
                name = ligne[0]
                if name == "TOTAL Inters Nord et Est":
                    break
                grade = "NULL" if str(ligne[1])=="nan" else ligne[1]
                site = "NULL" if str(ligne[2])=="nan" else ligne[2]
                week1 = ligne[3]
                week2 = ligne[4]
                week3 = ligne[5]
                week4 = ligne[6]
                week5 = ligne[7]
                week6 = ligne[8]
                week7 = ligne[9]
                week8 = ligne[10]
                week9 = ligne[11]
                week10 = ligne[12]
                week11 = ligne[13]
                success_rate = 0. if str(ligne[14])=="nan" else ligne[14]
                company = "NULL" if str(ligne[15])=="nan" else ligne[15]
                skills = "NULL" if str(ligne[16])=="nan" else ligne[16]
                cv_code = ligne[17]
                pe = "NULL" if str(ligne[18]) == "nan" else str(ligne[18])
                weeks= ()

                # generating weeks => Null if there're no info in warwait
                for _ in range(1, week_list[0]):
                    weeks = weeks + ("NULL",)

                weeks += (str(week1),str(week2),str(week3),str(week4),str(week5),str(week6),str(week7),str(week8),str(week9),str(week10),str(week11))
                for i,w in enumerate(weeks):
                    if w == "nan":
                        week[i] = "NULL"
                for _ in range(len(weeks)+1,53): ## range is lower <= x < upper
                    weeks = weeks + ("NULL",)
                params = (name,grade,site) + weeks + (success_rate,company,skills,str(34),pe,"false")


                ## Insert values into database
                try:
                    insert_warwait = "INSERT INTO warwait (nom, grade, site,s1,s2,s3,s4,s5,s6,s7,s8,s9,s10,s11,s12,s13,s14,s15,s16,s17,s18,s19,s20,s21,s22,s23,s24,s25,s26,s27,s28,s29,s30,s31,s32,s33,s34,s35,s36,s37,s38,s39,s40,s41,s42,s43,s44,s45,s46,s47,s48,s49,s50,s51,s52,reussite,positionnement,competences,cv_code,pe,en_mission) VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)"
                    cursor.execute(insert_warwait,params)
                except mysql.Error as e:
                    raise Exception("Cant insert in the table : {}".format(e))
                    
            database.commit()
            
            tk.messagebox.showinfo("Success","Warwait has been added")
            if database.is_connected():
                cursor.close()
                database.close()
                print("MySQL connection is closed")
        #///////////////////////////////////////////////////////////////// La matrice de compétence
        elif _type == "sm":

            try:
                database = mysql.connect(host=self.HOST,
                                        database=self.DATABASE[1],
                                        user=self.USER,
                                        password=self.PASSWORD)
                print("Connected to database... OK")
            except mysql.Error as e:
                raise Exception("Cant connect to the database : {}".format(e))
            cursor = database.cursor()
            db = pd.read_excel(path_to_excel, sheet_name="Matrice",skiprows=[0,3,9,30,38,46,57,67,77,82,89,102,115],nrows=114)

            db_list = db.to_numpy()
            names = db.columns.to_numpy()
            for i,n in enumerate(names):
                names[i] = n.replace("\u200b","")
           
            classes_name = [db_list[k][0].replace(',','').replace('#','sharp').replace('++','pp').replace(' ', '_').replace('.','_').replace('/','_').replace('\\','_').replace('-','_').replace('__','_').replace('__','_').replace("\xc2\xa0","").replace('(','').replace(')','').lower() for k in range(len(db_list))]
            
            construct_query="name, "+ classes_name[0] +", "

            construct_sql = "name TEXT, " + classes_name[0] + " TEXT, "
            for i in range(1, len(classes_name)-1):
                construct_sql += classes_name[i] + " INTEGER, "
                construct_query += classes_name[i] + " , "

            construct_sql += "Niveau_Anglais TEXT"
            construct_query += "Niveau_Anglais"
            

            create_table_sm = "CREATE TABLE IF NOT EXISTS skillmatrix (name TEXT, disponibilite TEXT, aws INTEGER, azure INTEGER, gcp INTEGER, nutanix INTEGER, oracle_cloud INTEGER, powershell INTEGER, bash INTEGER, javascript INTEGER, angular INTEGER, node_js INTEGER, python INTEGER, vba INTEGER, rust INTEGER, csharp INTEGER, cpp INTEGER, c INTEGER, sqlb INTEGER, pl_sql INTEGER, ruby INTEGER, java INTEGER, php INTEGER, html_css INTEGER, perl INTEGER, api_rest INTEGER, asp_net INTEGER, anglais INTEGER, agile INTEGER, safe INTEGER, gestion_de_projet_cycle_en_v INTEGER, itil INTEGER, pmp INTEGER, prince_two INTEGER, kafka INTEGER, apache2_httpd INTEGER, nginx INTEGER, iis_windows_server INTEGER, tomcat INTEGER, ldap INTEGER, active_directory INTEGER, centreon INTEGER, datadog INTEGER, elk INTEGER, nagios INTEGER, zabbix INTEGER, prometheus INTEGER, splunk INTEGER, ca_spectrum INTEGER, hpe_imc INTEGER, grafana_loki INTEGER, ansible INTEGER, git_automate INTEGER, jenkins INTEGER, kubernetes INTEGER, automator INTEGER, universe INTEGER, ctrl_m INTEGER, vtom INTEGER, terraform INTEGER, windows_server INTEGER, unix_bsd_solaris INTEGER, linux INTEGER, saltstack_gestionnaire_de_conf_unifie INTEGER, vmware INTEGER, vmware_vra_vro INTEGER, veeam INTEGER, backup_exec INTEGER, docker INTEGER, mysql_mariadb INTEGER, oracle INTEGER, postresql INTEGER, sql_server INTEGER, citrix INTEGER, hyper_v INTEGER, proxmox INTEGER, virtualbox INTEGER, qemu INTEGER, vmware_sys INTEGER, jira INTEGER, servicenow INTEGER, trello INTEGER, ms_visio INTEGER, photoshop INTEGER, ms_project INTEGER, ocs_inventory INTEGER, git INTEGER, office_365 INTEGER, sonarqube INTEGER, microsoft_exchange INTEGER, teradata INTEGER, openvas INTEGER, kali_linux INTEGER, parrot_os INTEGER, firewall_checkpoint INTEGER, pfsense INTEGER, cisco_meraki INTEGER, crowdstrike INTEGER, sophos INTEGER, paloalto INTEGER, cisco INTEGER, dns INTEGER, dhcp INTEGER, Niveau_Anglais TEXT)"

            
            cursor.execute("DROP TABLE IF EXISTS skillmatrix")
            cursor.execute(create_table_sm)

            # "transposition de matrice" (faire un quart de tour) pour que les noms correspondes aux lignes et les compétences aux colones
            for j in tqdm(range(len(names)-1)):
                sleep(0.1)
                tp_res = ()
                for k in range(1,len(db_list)-1):
                    if type(db_list[k][j+1]!= str):
                        tp_res += (int(0),) if isnan(db_list[k][j+1]) else (int(db_list[k][j+1]),) 
                if type(db_list[len(db_list)-1][j+1]) == str:
                    lvlenglish = (str(db_list[len(db_list)-1][j+1]),)
                else:
                    lvlenglish = ("NULL",) if isnan(db_list[len(db_list)-1][j+1]) else (str(db_list[len(db_list)-1][j+1]),)
                tp_res = (str(names[j+1]), str(db_list[0][j+1])) + tp_res + lvlenglish
                
                tmp = "name TEXT, disponibilite TEXT, aws INTEGER, azure INTEGER, gcp INTEGER, nutanix INTEGER, oracle_cloud INTEGER, powershell INTEGER, bash INTEGER, javascript INTEGER, angular INTEGER, node_js INTEGER, python INTEGER, vba INTEGER, rust INTEGER, csharp INTEGER, cpp INTEGER, c INTEGER, sqlb INTEGER, pl_sql INTEGER, ruby INTEGER, java INTEGER, php INTEGER, html_css INTEGER, perl INTEGER, api_rest INTEGER, asp_net INTEGER, anglais INTEGER, agile INTEGER, safe INTEGER, gestion_de_projet_cycle_en_v INTEGER, itil INTEGER, pmp INTEGER, prince_two INTEGER, kafka INTEGER, apache2_httpd INTEGER, nginx INTEGER, iis_windows_server INTEGER, tomcat INTEGER, ldap INTEGER, active_directory INTEGER, centreon INTEGER, datadog INTEGER, elk INTEGER, nagios INTEGER, zabbix INTEGER, prometheus INTEGER, splunk INTEGER, ca_spectrum INTEGER, hpe_imc INTEGER, grafana_loki INTEGER, ansible INTEGER, git_automate INTEGER, jenkins INTEGER, kubernetes INTEGER, automator INTEGER, universe INTEGER, ctrl_m INTEGER, vtom INTEGER, terraform INTEGER, windows_server INTEGER, unix_bsd_solaris INTEGER, linux INTEGER, saltstack_gestionnaire_de_conf_unifie INTEGER, vmware INTEGER, vmware_vra_vro INTEGER, veeam INTEGER, backup_exec INTEGER, docker INTEGER, mysql_mariadb INTEGER, oracle INTEGER, postresql INTEGER, sql_server INTEGER, citrix INTEGER, hyper_v INTEGER, proxmox INTEGER, virtualbox INTEGER, qemu INTEGER, vmware_sys INTEGER, jira INTEGER, servicenow INTEGER, trello INTEGER, ms_visio INTEGER, photoshop INTEGER, ms_project INTEGER, ocs_inventory INTEGER, git INTEGER, office_365 INTEGER, sonarqube INTEGER, microsoft_exchange INTEGER, teradata INTEGER, openvas INTEGER, kali_linux INTEGER, parrot_os INTEGER, firewall_checkpoint INTEGER, pfsense INTEGER, cisco_meraki INTEGER, crowdstrike INTEGER, sophos INTEGER, paloalto INTEGER, cisco INTEGER, dns INTEGER, dhcp INTEGER, Niveau_Anglais TEXT"
                tmp = tmp.replace("TEXT", "").replace("INTEGER","")
                
                params = "\'{}\',\'{}\',"
                for i in range(2,len(classes_name)):
                    params += "{},"

                params+="\'{}\'"

                insert_query = "INSERT INTO skillmatrix (" + tmp + ") VALUES ("+params+")"
                
                
                cursor.execute(self.fill_query(insert_query,tp_res))
            database.commit()
            tk.messagebox.showinfo("Success","Skills matrix has been added")
        else :
            raise Exception("Unknwon type of database")


    def fill_query(self, src, tp):
        i = 0
        j = 0
        while i < len(src) and len(tp)>0:
            c = src[i]
            if c == "{":                
                j = i+len(str(tp[0]))+1
                src = src[:i] + str(tp[0]) + src[i+2:]
                tp = tp[1:]
                i=j
            else:
                i +=1
        return src


if __name__ == "__main__":
    setup = Setup()
    setup.mainloop()
