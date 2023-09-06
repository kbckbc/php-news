## 2022 Fall, CSE330, Module 3 Group project
  - Byeongchan Gwak, 501026, kbckbc
  - This is done by only me.

## Go to the NEWS SITE
  - [**Click to the site**](http://bcgwak.godohosting.com/php-news/list.php)
  - I added 2 users. Plz use below users to check my project.
  - jack@wustl.edu / jack
  - rob@wustl.edu / rob
  - And I already upload news to the site.
  
## Tables
  - [**Go to users.sql**](https://github.com/cse330-fall-2022/module3-group-module3-501026/blob/master/users.sql)
  - [**Go to stories.sql**](https://github.com/cse330-fall-2022/module3-group-module3-501026/blob/master/stories.sql)
  - [**Go to comments.sql**](https://github.com/cse330-fall-2022/module3-group-module3-501026/blob/master/comments.sql)


## Creative Portion (15 Points)

  - The site support 'Pagination' function. How many news article we have, we can navigate through Pagination!
  - The site support 'My page' function. User can see their information through a 'My page'!
  - The site support 'Delete account' function. Users can sign out the site if they want!
  - The site support 'Prevention of duplicate signup' function. When sign up, the site check there is a user already has the same Username.
  
## A note about creative portion

### Pagination
  - 'Pagination' is just a navigate number of pages.
  - I thought that if the site has tons of news article, it gets harder to navigate through the news.
  - To implement 'Pagination', I need to calculate the number of what page is the user looking at, and what page block is the user in, so and so forth.

### My page
  - User can check their Username, Nickname, Join date, Last login date on the 'My page'
  - Whenever user log in, the site update the users' log in time

### Delete account
  - User can leave the site if they want.
  - It deletes all the comments and stories of the user

### Prevention of duplicate signup
  - User can not signup the same name.
  - When click 'Sign up', the site check whether the username happens to be the same.
  
## Grading
-10pts cannot post, edit or delete stories\
-5pts cannot delete account
