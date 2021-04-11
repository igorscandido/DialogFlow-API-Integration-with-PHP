<?php

// Google Cloud DialogFlow Libraries
use Google\Cloud\Dialogflow\V2\SessionsClient;
use Google\Cloud\Dialogflow\V2\TextInput;
use Google\Cloud\Dialogflow\V2\QueryInput;

class DialogFlowController
{
    public function SendIntent()
    {

        // Here you call the .json that can be generated in dialogflow console
        // After that you just set an ENV pointing to the file
        // The ENV aways need to be the same as put down in the code
        if(getenv('GOOGLE_APPLICATION_CREDENTIALS') !== null)
        {   
            putenv('GOOGLE_APPLICATION_CREDENTIALS=' . 'The directory in which you saved the .json file' );
        }

        try{

            // ID of the project the Bot was created at
            $projectID = '';

            // A session key for keeping the conversation 
            // This needs to be the same for everytime that the user
            // will be keeping the conversation going on
            $sessionID = '';

            // The question the user submited to the bot
            $userIntent = '';

            // The type of language the user will be asking in
            $languageCode = '';

            // Defines and create a session between DialogFlow and the User
            $sessionsClient = new SessionsClient();
            $session = $sessionsClient->sessionName($projectID, $sessionID);

            // Creates a new intent with the user message and type of language the bot will need to understand
            $textInput = new TextInput();
            $textInput->setText($userIntent);
            $textInput->setLanguageCode($languageCode);

            // Creates the query that will be sent using the session connection
            // We only set the last message the user has sent 
            $queryInput = new QueryInput();
            $queryInput->setText($textInput);

            // Detects the intent that the bot will need to use in order to respond to the user
            $response = $sessionsClient->detectIntent($session, $queryInput);

            // Makes the query and waits for a response
            // As we are using a try...catch loop there's no need to check
            // Unless you want to point it to the user
            $queryResult = $response->getQueryResult();

            // Get's all the informations the bot can provide //

            $queryText = $queryResult->getQueryText();
            
            $intent = $queryResult->getIntent(); // The message the user sent

            $displayName = $intent->getDisplayName(); // The Agent / Intent the bot used to send a response

            $confidence = $queryResult->getIntentDetectionConfidence(); // The confidence the bot had for that question and answer

            $fulfilmentText = $queryResult->getFulfillmentText(); // The message the bot returned back to the user as a response

            return [
                'queryResult' => $queryResult,
                'queryText' => $queryText,
                'displayName' => $displayName,
                'confidence' => $confidence,
                'fulfilmentText' => $fulfilmentText 
            ];

        }catch(Exception $error)
        {

            if ($userIntent == '')
            {
                return [
                    'fulfilmentText' => 'Sorry, you need to send a message to get an apropriate answer'
                ];

            }else{

                return [
                    'fulfilmentText' => "Sorry, I'm currently unavailable" 
                ];
                
            }
            
        }

   
    }
}
