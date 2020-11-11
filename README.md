## Planning Managemnet System

## Idea Brief

This idea it to help users to manage thier life, by only drop thier to do list to the system and the system will tell them if they are free or not,
also will return a full detaild of thier plans and will allow users to search and filter them by date.

It provide them also to write feedbacks for thier plans, so they can alwyas know what they felt for that event and if they have changed from before.


## Packages used

- Only passport for authenications, and later will use Spatie for roles and permissions for more details.

## How to use this project

After composer install, key generations, update .env file and migrate

1- Run php artisan passport:install

2- AuthController
    - (POST) your_url/api/signup  -> name,email,password

    - (POST) your_url/api/login -> email,password 
        - This will return a token to user it. (Ex. Bearer token_returned_from_api)

    - (GET) your_url/api/logout

3- PlansController
    - (GET) your_url/api/plans/  -> will return current user plans that user can filter with (start_date, due_date and search by name) and with every plan feedback

    - (GET) your_url/api/plans/1 -> will return plan with id 1

    - (POST) your_url/api/plans/  -> to create a new plan with those parameters (name,description,start_date,due_date)

    - (PATCH) your_url/api/plans/1 -> will update any parameter for plan with id 1

    - (DELETE) your_url/api/plans/1 -> will delete plan with id 1

4- FeedbackController
    - (GET) your_url/api/feedbacks/  -> will return current user feedbacks with plans assigned to it

    - (GET) your_url/api/feedbacks/1 -> will return feedback with id 1

    - (POST) your_url/api/feedbacks/  -> to create a new feedback with those parameters (feedback,rate,plan_id)

    - (PATCH) your_url/api/feedbacks/1 -> will update any parameter for feedback with id 1 (except plan_id)

    - (DELETE) your_url/api/feedbacks/1 -> will delete feedback with id 1



## Thank you