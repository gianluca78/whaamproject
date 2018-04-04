Whaam application (the project is no longer maintained)
=======================================================
The **WHAAM** (Web Health Application for Adhd Monitoring) project is aimed at diffusing a new culture in the ADHD intervention.
The rationale is that, technology can promote a better networking among people involved in the care of an ADHD child: parents,
teachers and health professionals (HPs). During the project, different outcomes have been released, including a theoretical
Framework (CDFw), an application to support multimodal interventions for ADHD subjects, a training course for parents,
teachers and HPs to familiarize them with the cognitive-behavioural techniques and the use of the Web App created.
Finally, a training module have been designed, aimed at bringing the theme of ADHD and the use of Web App into University curricula.
The WHAAM project has been funded with the support from European Commission, in the context of the Lifelong Learning Programme
Key Activity 3 Multilateral Projects.

http://www.whaamproject.eu

Requirements (between brackets the last tested versions)
--------------------------------------------------------

* Symfony 2.6.5 (included in the repository)
* Apache 2.4 (2.4.18)
* php 5.6 (5.6.34) with the following packages: mbstring, mcrypt, mysql, xml, intl
* mysql 14 (14.14)
* R 3.2 (3.2.3)

The project originally used Composer as Dependency Manager for PHP. Considering that the WHAAM application is not more
maintained, please do not use Composer to update the vendors to avoid compatibility issues. The vendors are included in the repository.

Installing R for the statistical analyses
-----------------------------------------

Install R with the following packages (installing the packages in a common path so as to let Apache to access them):

* Kendall (install.packages("Kendall", lib="/usr/local/lib/R/site-library"))
* RJSONIO (install.packages("RJSONIO", lib="/usr/local/lib/R/site-library"))

Give the write permissions for your Apache user to the following folders
------------------------------------------------------------------------

* app/cache
* app/logs
* app/data

Copy the app/config/parameters.yml.dist, rename it to parameters.yml and fill in it with your personal data.
------------------------------------------------------------------------------------------------------------

* auth_api_doc: #api key to access the webservice documentation
* auth_api_key_dev: #api key to test the mobile application
* auth_api_key_prod: #api key used in production by the mobile application
* test_api_key: #api key for unit and functional tests
* data_r_file_path: #absolute path to the folder app/data/R/assessment
* tau_script_path: #absolute path to the file src/WHAAM/PrivateApplication/Bundle/ChildBehaviorAssessmentBundle/Util/R/tauSystem.R

Add the tau_script_path at the beginning of the file src/WHAAM/PrivateApplication/Bundle/ChildBehaviorAssessmentBundle/Util/R/tauSystem.R
-----------------------------------------------------------------------------------------------------------------------------------------------

Create the database and load the fixtures with the following commands from terminal (assuming that you are using a php5.6 binary)
-----------------------------------------------------------------------------------------------------------------------------------

* php5.6 app/console doctrine:database:create
* php5.6 app/console doctrine:schema:update --force
* php5.6 app/console doctrine:fixtures:load

A test user is created with fake data. You will access this account data using test as both username and password.

Ok, everything should be ready now. Please go to

http://localhost/whaam/web/app.php/login

and login with the test user.

Web service documentation
-------------------------

The address of the api doc is:

http://localhost/whaam/web/app.php/api/doc/

You can access with the user AUTH_API_DOC and the password you entered in parameters.yml.
