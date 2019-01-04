## Run Test

Main Folder in Windows 10

1. php bin/phpunit

---

## TDD - Unit testing with PHPUnit

1. "unit testing is a software testing method by which individual units of source code [...] are tested to determine whether they aare fit for use."
2. No testing user flow
3. Not testing controllers
4. Testing individual units (i.e. individual classes that do one job)
5. Acceptance testing of business logic
6. Integration testing
7. The goal of unit testing is "to isolate each part of the program and show that the individual parts are correct"
8. Testing is fun. Sometimes


---

## More info about test (Copy-Past)

### Formularz rejestracji

1. Nick
	- Pole nick nie może być puste
- Długość nicka musi być dłuższa niż 3 znaki
- Długość nicka musi być krótsza niż 20 znaków
- Nick musi mieć co najmniej jedną, dużą literę (polski alfabet)
- Nick musi mieć liczbę

2. Adres Email 
	- Pole email nie może być puste
- Format: adres@xyz.com
- Adres email musi zawierać @
- Część po znaku @ musi zawierać kropkę

3. Hasło, Hasło 2
	- Pole hasło i hasło 2 musi być identyczne
- Hasło musi mieć dużą literę (polski alfabet)
- Hasło musi mieć liczbę 
- Hasło musi być krótsze niż 20 znaków
- Hasło musi być dłuższe niż 3 znak

4. Regulamin	
	- Użytkownik musi zaakceptować regulamin

5. Link aktywacyjny
	- Żeby zakończyć rejestrację należy aktywować konto za pomocą linku aktywacyjnego, który został wysłany na pocztę elektroniczną
- Bezterminowy link, konto można aktywować w dowolnym czasie

6. Zasady ogólne
	- Formularz Rejestracji musi być responsywny (min. do 320px) 
- Użytkownik nie może dwukrotnie utworzyć konta z tym samym adresem email lub nazwą użytkownika
- Formularz powinien być odporny na  SQL INJECTION
- Użytkownik powinien zostać poinformowany o pomyślnym wysłaniu hiperłącza aktywacyjnego na adres Email oraz pomyślnym utworzeniu konta w serwisie 
- Użytkownik nie może kliknąć guzika “ZAłÓŻ KONTO” w trakcie walidacji aktualnego żądania
- Hasło jest szyfrowane "argon2i"
- RWD

### Formularz logowania

1. Pole Login
	- Na konto można się zalogować przy pomocy Nicku lub adresu email
2. Pole Hasło
	- Użytkownik podaje hasło, które ustalił przy rejestracji konta
- Wprowadzone hasło w formularzu musi być zgodne z hasłem w bazie danych. Wpisane hasło zamieniane jest na funkcję skrótu “argon2i” i porównywane w bazie danych z hasłem ustalonym przy rejestracji.
3. Zasady ogólne
	- W wyniku poprawnego zalogowania, użytkownik przenoszony jest na wewnętrzną stronę serwisu
- Poprawne logowanie może się odbyć tylko w momencie, kiedy użytkownik aktywował konto przy pomocy hiperłącza aktywacyjnego, wysłanego na konto pocztowe
- W wyniku podania błędnych danych logowania użytkownik powinien zostać poinformowany, czy dane logowania są błędne, czy być może konto nie zostało jeszcze aktywowane
- Formularz powinien być odporny na SQL INJECTION
- Ochrona csrf
- RWD

---

## Clone a repository

Use these steps to clone from SourceTree, our client for using the repository command-line free. Cloning allows you to work on your files locally. If you don't yet have SourceTree, [download and install first](https://www.sourcetreeapp.com/). If you prefer to clone from the command line, see [Clone a repository](https://confluence.atlassian.com/x/4whODQ).

1. You’ll see the clone button under the **Source** heading. Click that button.
2. Now click **Check out in SourceTree**. You may need to create a SourceTree account or log in.
3. When you see the **Clone New** dialog in SourceTree, update the destination path and name if you’d like to and then click **Clone**.
4. Open the directory you just created to see your repository’s files.

Now that you're more familiar with your Bitbucket repository, go ahead and add a new file locally. You can [push your change back to Bitbucket with SourceTree](https://confluence.atlassian.com/x/iqyBMg), or you can [add, commit,](https://confluence.atlassian.com/x/8QhODQ) and [push from the command line](https://confluence.atlassian.com/x/NQ0zDQ).