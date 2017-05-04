<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Bundle\CoreBundle\Command\Activiti;

use Eulogix\Cool\Lib\Symfony\Console\CoolCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;

/**
 * Class used to build a skeleton of an Activiti client, from the public REST interface documentation
 * Not meant to be used any more, use the generated client instead (in eulogix/libs-activiti)
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class GenerateClientCommand extends CoolCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('cool:activiti:generateClient')
            ->setDescription('Generates the skeleton of the client');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->validate($input);


        $rawDump = [[
        <<<EOF
        List of Deployments
EOF
        , 'GET repository/deployments', 'GET', 'repository/deployments',
        <<<EOF

Table 15.10. URL query parameters
Parameter	Required	Value	Description
name	No	String	Only return deployments with the given name.
nameLike	No	String	Only return deployments with a name like the given name.
category	No	String	Only return deployments with the given category.
categoryNotEquals	No	String	Only return deployments which don't have the given category.
tenantId	No	String	Only return deployments with the given tenantId.
tenantIdLike	No	String	Only return deployments with a tenantId like the given value.
withoutTenantId	No	Boolean	If true, only returns deployments without a tenantId set. If false, the withoutTenantId parameter is ignored.
sort	No	'id' (default), 'name', 'deploytime' or 'tenantId'	Property to sort on, to be used together with the 'order'.
The general paging and sorting query-parameters can be used for this URL.


Table 15.11. REST Response codes
Response code	Description
200	Indicates the request was successful.

Success response body:

{
  "data": [
    {
      "id": "10",
      "name": "activiti-examples.bar",
      "deploymentTime": "2010-10-13T14:54:26.750+02:00",
      "category": "examples",
      "url": "http://localhost:8081/service/repository/deployments/10",
      "tenantId": null
    }
  ],
  "total": 1,
  "start": 0,
  "sort": "id",
  "order": "asc",
  "size": 1
}

EOF
    ],
        [
            <<<EOF
            Get a deployment
EOF
            , 'GET repository/deployments/{deploymentId}', 'GET', 'repository/deployments/{deploymentId}',
            <<<EOF

Table 15.12. Get a deployment - URL parameters
Parameter	Required	Value	Description
deploymentId	Yes	String	The id of the deployment to get.

Table 15.13. Get a deployment - Response codes
Response code	Description
200	Indicates the deployment was found and returned.
404	Indicates the requested deployment was not found.

Success response body:

{
  "id": "10",
  "name": "activiti-examples.bar",
  "deploymentTime": "2010-10-13T14:54:26.750+02:00",
  "category": "examples",
  "url": "http://localhost:8081/service/repository/deployments/10",
  "tenantId" : null
}

EOF
        ],
        [
            <<<EOF
            Create a new deployment
EOF
            , 'POST repository/deployments', 'POST', 'repository/deployments',
            <<<EOF

Request body:

The request should body should contain data of type multipart/form-data. There should be only exactly file in the request, any additional files will be ignored. The deployment name is the name of the file-field passed in. If multiple resources need to be deployed in a single deployment, compress the resources in a zip and make sure the file-name ends with .bar or .zip.

An additional parameter (form-field) can be passed in the request body with name tenantId. The value of this field will be used as the id of the tenant this deployment is done in.

Table 15.14. Create a new deployment - Response codes
Response code	Description
201	Indicates the deployment was created.
400	Indicates there was no content present in the request body or the content mime-type is not supported for deployment. The status-description contains additional information.

Success response body:

{
  "id": "10",
  "name": "activiti-examples.bar",
  "deploymentTime": "2010-10-13T14:54:26.750+02:00",
  "category": null,
  "url": "http://localhost:8081/service/repository/deployments/10",
  "tenantId" : "myTenant"
}

EOF
        ],
        [
            <<<EOF
            Delete a deployment
EOF
            , 'DELETE repository/deployments/{deploymentId}', 'DELETE', 'repository/deployments/{deploymentId}',
            <<<EOF

Table 15.15. Delete a deployment - URL parameters
Parameter	Required	Value	Description
deploymentId	Yes	String	The id of the deployment to delete.

Table 15.16. Delete a deployment - Response codes
Response code	Description
204	Indicates the deployment was found and has been deleted. Response-body is intentionally empty.
404	Indicates the requested deployment was not found.


EOF
        ],
        [
            <<<EOF
            List resources in a deployment
EOF
            , 'GET repository/deployments/{deploymentId}/resources', 'GET', 'repository/deployments/{deploymentId}/resources',
            <<<EOF

Table 15.17. List resources in a deployment - URL parameters
Parameter	Required	Value	Description
deploymentId	Yes	String	The id of the deployment to get the resources for.

Table 15.18. List resources in a deployment - Response codes
Response code	Description
200	Indicates the deployment was found and the resource list has been returned.
404	Indicates the requested deployment was not found.

Success response body:

[
  {
    "id": "diagrams/my-process.bpmn20.xml",
    "url": "http://localhost:8081/activiti-rest/service/repository/deployments/10/resources/diagrams%2Fmy-process.bpmn20.xml",
    "dataUrl": "http://localhost:8081/activiti-rest/service/repository/deployments/10/resourcedata/diagrams%2Fmy-process.bpmn20.xml",
    "mediaType": "text/xml",
    "type": "processDefinition"
  },
  {
    "id": "image.png",
    "url": "http://localhost:8081/activiti-rest/service/repository/deployments/10/resources/image.png",
    "dataUrl": "http://localhost:8081/activiti-rest/service/repository/deployments/10/resourcedata/image.png",
    "mediaType": "image/png",
    "type": "resource"
  }
]
mediaType: Contains the media-type the resource has. This is resolved using a (pluggable) MediaTypeResolver and contains, by default, a limited number of mime-type mappings.
type: Type of resource, possible values:
resource: Plain old resource.
processDefinition: Resource that contains one or more process-definitions. This resource is picked up by the deployer.
processImage: Resource that represents a deployed process definition's graphical layout.
The dataUrl property in the resulting json for a single resource contains the actual URL to use for retrieving the binary resource.


EOF
        ],
        [
            <<<EOF
            Get a deployment resource
EOF
            , 'GET repository/deployments/{deploymentId}/resources/{resourceId}', 'GET', 'repository/deployments/{deploymentId}/resources/{resourceId}',
            <<<EOF

Table 15.19. Get a deployment resource - URL parameters
Parameter	Required	Value	Description
deploymentId	Yes	String	The id of the deployment the requested resource is part of.
resourceId	Yes	String	The id of the resource to get. Make sure you URL-encode the resourceId in case it contains forward slashes. Eg: use 'diagrams%2Fmy-process.bpmn20.xml' instead of 'diagrams/Fmy-process.bpmn20.xml'.

Table 15.20. Get a deployment resource - Response codes
Response code	Description
200	Indicates both deployment and resource have been found and the resource has been returned.
404	Indicates the requested deployment was not found or there is no resource with the given id present in the deployment. The status-description contains additional information.

Success response body:

{
  "id": "diagrams/my-process.bpmn20.xml",
  "url": "http://localhost:8081/activiti-rest/service/repository/deployments/10/resources/diagrams%2Fmy-process.bpmn20.xml",
  "dataUrl": "http://localhost:8081/activiti-rest/service/repository/deployments/10/resourcedata/diagrams%2Fmy-process.bpmn20.xml",
  "mediaType": "text/xml",
  "type": "processDefinition"
}
mediaType: Contains the media-type the resource has. This is resolved using a (pluggable) MediaTypeResolver and contains, by default, a limited number of mime-type mappings.
type: Type of resource, possible values:
resource: Plain old resource.
processDefinition: Resource that contains one or more process-definitions. This resource is picked up by the deployer.
processImage: Resource that represents a deployed process definition's graphical layout.

EOF
        ],
        [
            <<<EOF
            Get a deployment resource content
EOF
            , 'GET repository/deployments/{deploymentId}/resourcedata/{resourceId}', 'GET', 'repository/deployments/{deploymentId}/resourcedata/{resourceId}',
            <<<EOF

Table 15.21. Get a deployment resource content - URL parameters
Parameter	Required	Value	Description
deploymentId	Yes	String	The id of the deployment the requested resource is part of.
resourceId	Yes	String	The id of the resource to get the data for. Make sure you URL-encode the resourceId in case it contains forward slashes. Eg: use 'diagrams%2Fmy-process.bpmn20.xml' instead of 'diagrams/Fmy-process.bpmn20.xml'.

Table 15.22. Get a deployment resource content - Response codes
Response code	Description
200	Indicates both deployment and resource have been found and the resource data has been returned.
404	Indicates the requested deployment was not found or there is no resource with the given id present in the deployment. The status-description contains additional information.

Success response body:

The response body will contain the binary resource-content for the requested resource. The response content-type will be the same as the type returned in the resources 'mimeType' property. Also, a content-disposition header is set, allowing browsers to download the file instead of displaying it.

Process Definitions

EOF
        ],
        [
            <<<EOF
            List of process definitions
EOF
            , 'GET repository/process-definitions', 'GET', 'repository/process-definitions',
            <<<EOF

Table 15.23. List of process definitions - URL parameters
Parameter	Required	Value	Description
version	No	integer	Only return process definitions with the given version.
name	No	String	Only return process definitions with the given name.
nameLike	No	String	Only return process definitions with a name like the given name.
key	No	String	Only return process definitions with the given key.
keyLike	No	String	Only return process definitions with a name like the given key.
resourceName	No	String	Only return process definitions with the given resource name.
resourceNameLike	No	String	Only return process definitions with a name like the given resource name.
category	No	String	Only return process definitions with the given category.
categoryLike	No	String	Only return process definitions with a category like the given name.
categoryNotEquals	No	String	Only return process definitions which don't have the given category.
deploymentId	No	String	Only return process definitions which are part of a deployment with the given id.
startableByUser	No	String	Only return process definitions which can be started by the given user.
latest	No	Boolean	Only return the latest process definition versions. Can only be used together with 'key' and 'keyLike' parameters, using any other parameter will result in a 400-response.
suspended	No	Boolean	If true, only returns process definitions which are suspended. If false, only active process definitions (which are not suspended) are returned.
sort	No	'name' (default), 'id', 'key', 'category', 'deploymentId' and 'version'	Property to sort on, to be used together with the 'order'.
The general paging and sorting query-parameters can be used for this URL.



Table 15.24. List of process definitions - Response codes
Response code	Description
200	Indicates request was successful and the process-definitions are returned
400	Indicates a parameter was passed in the wrong format or that 'latest' is used with other parameters other than 'key' and 'keyLike'. The status-message contains additional information.

Success response body:

{
  "data": [
    {
      "id" : "oneTaskProcess:1:4",
      "url" : "http://localhost:8182/repository/process-definitions/oneTaskProcess%3A1%3A4",
      "version" : 1,
      "key" : "oneTaskProcess",
      "category" : "Examples",
      "suspended" : false,
      "name" : "The One Task Process",
      "description" : "This is a process for testing purposes",
      "deploymentId" : "2",
      "deploymentUrl" : "http://localhost:8081/repository/deployments/2",
      "graphicalNotationDefined" : true,
      "resource" : "http://localhost:8182/repository/deployments/2/resources/testProcess.xml",
      "diagramResource" : "http://localhost:8182/repository/deployments/2/resources/testProcess.png",
      "startFormDefined" : false
    }
  ],
  "total": 1,
  "start": 0,
  "sort": "name",
  "order": "asc",
  "size": 1
}
graphicalNotationDefined: Indicates the process definition contains graphical information (BPMN DI).
resource: Contains the actual deployed BPMN 2.0 xml.
diagramResource: Contains a graphical representation of the process, null when no diagram is available.

EOF
        ],
        [
            <<<EOF
            Get a process definition
EOF
            , 'GET repository/process-definitions/{processDefinitionId}', 'GET', 'repository/process-definitions/{processDefinitionId}',
            <<<EOF

Table 15.25. Get a process definition - URL parameters
Parameter	Required	Value	Description
processDefinitionId	Yes	String	The id of the process definition to get.

Table 15.26. Get a process definition - Response codes
Response code	Description
200	Indicates the process definition was found and returned.
404	Indicates the requested process definition was not found.

Success response body:

{
  "id" : "oneTaskProcess:1:4",
  "url" : "http://localhost:8182/repository/process-definitions/oneTaskProcess%3A1%3A4",
  "version" : 1,
  "key" : "oneTaskProcess",
  "category" : "Examples",
  "suspended" : false,
  "name" : "The One Task Process",
  "description" : "This is a process for testing purposes",
  "deploymentId" : "2",
  "deploymentUrl" : "http://localhost:8081/repository/deployments/2",
  "graphicalNotationDefined" : true,
  "resource" : "http://localhost:8182/repository/deployments/2/resources/testProcess.xml",
  "diagramResource" : "http://localhost:8182/repository/deployments/2/resources/testProcess.png",
  "startFormDefined" : false
}
graphicalNotationDefined: Indicates the process definition contains graphical information (BPMN DI).
resource: Contains the actual deployed BPMN 2.0 xml.
diagramResource: Contains a graphical representation of the process, null when no diagram is available.

EOF
        ],
        [
            <<<EOF
            Update category for a process definition
EOF
            , 'PUT repository/process-definitions/{processDefinitionId}', 'PUT', 'repository/process-definitions/{processDefinitionId}',
            <<<EOF

Body JSON:

{
  "category" : "updatedcategory"
}
Table 15.27. Update category for a process definition - Response codes
Response code	Description
200	Indicates the process was category was altered.
400	Indicates no category was defined in the request body.
404	Indicates the requested process definition was not found.

Success response body: see response for repository/process-definitions/{processDefinitionId}.


EOF
        ],
        [
            <<<EOF
            Get a process definition resource content
EOF
            , 'GET repository/process-definitions/{processDefinitionId}/resourcedata', 'GET', 'repository/process-definitions/{processDefinitionId}/resourcedata',
            <<<EOF

Table 15.28. Get a process definition resource content - URL parameters
Parameter	Required	Value	Description
processDefinitionId	Yes	String	The id of the process definition to get the resource data for.

Response:

Exactly the same response codes/boy as GET repository/deployment/{deploymentId}/resourcedata/{resourceId}.


EOF
        ],
        [
            <<<EOF
            Get a process definition BPMN model
EOF
            , 'GET repository/process-definitions/{processDefinitionId}/model', 'GET', 'repository/process-definitions/{processDefinitionId}/model',
            <<<EOF

Table 15.29. Get a process definition BPMN model - URL parameters
Parameter	Required	Value	Description
processDefinitionId	Yes	String	The id of the process definition to get the model for.

Table 15.30. Get a process definition BPMN model - Response codes
Response code	Description
200	Indicates the process definition was found and the model is returned.
404	Indicates the requested process definition was not found.

Response body: The response body is a JSON representation of the org.activiti.bpmn.model.BpmnModel and contains the full process definition model.

{
   "processes":[
      {
         "id":"oneTaskProcess",
         "xmlRowNumber":7,
         "xmlColumnNumber":60,
         "extensionElements":{

         },
         "name":"The One Task Process",
         "executable":true,
         "documentation":"One task process description",

         ...
    ],

    ...
}

EOF
        ],
        [
            <<<EOF
            Suspend a process definition
EOF
            , 'PUT repository/process-definitions/{processDefinitionId}', 'PUT', 'repository/process-definitions/{processDefinitionId}',
            <<<EOF

Body JSON:

{
  "action" : "suspend",
  "includeProcessInstances" : "false",
  "date" : "2013-04-15T00:42:12Z"
}
Table 15.31. Suspend a process definition - JSON Body parameters
Parameter	Description	Required
action	Action to perform. Either activate or suspend.	Yes
includeProcessInstances	Whether or not to suspend/activate running process-instances for this process-definition. If omitted, the process-instances are left in the state they are.	No
date	Date (ISO-8601) when the suspension/activation should be executed. If omitted, the suspend/activation is effective immediatly.	No

Table 15.32. Suspend a process definition - Response codes
Response code	Description
200	Indicates the process was suspended.
404	Indicates the requested process definition was not found.
409	Indicates the requested process definition is already suspended.

Success response body: see response for repository/process-definitions/{processDefinitionId}.


EOF
        ],
        [
            <<<EOF
            Activate a process definition
EOF
            , 'PUT repository/process-definitions/{processDefinitionId}', 'PUT', 'repository/process-definitions/{processDefinitionId}',
            <<<EOF

Body JSON:

{
  "action" : "activate",
  "includeProcessInstances" : "true",
  "date" : "2013-04-15T00:42:12Z"
}
See suspend process definition JSON Body parameters.

Table 15.33. Activate a process definition - Response codes
Response code	Description
200	Indicates the process was activated.
404	Indicates the requested process definition was not found.
409	Indicates the requested process definition is already active.

Success response body: see response for repository/process-definitions/{processDefinitionId}.


EOF
        ],
        [
            <<<EOF
            Get all candidate starters for a process-definition
EOF
            , 'GET repository/process-definitions/{processDefinitionId}/identitylinks', 'GET', 'repository/process-definitions/{processDefinitionId}/identitylinks',
            <<<EOF

Table 15.34. Get all candidate starters for a process-definition - URL parameters
Parameter	Required	Value	Description
processDefinitionId	Yes	String	The id of the process definition to get the identity links for.

Table 15.35. Get all candidate starters for a process-definition - Response codes
Response code	Description
200	Indicates the process definition was found and the requested identity links are returned.
404	Indicates the requested process definition was not found.

Success response body:

[
   {
      "url":"http://localhost:8182/repository/process-definitions/oneTaskProcess%3A1%3A4/identitylinks/groups/admin",
      "user":null,
      "group":"admin",
      "type":"candidate"
   },
   {
      "url":"http://localhost:8182/repository/process-definitions/oneTaskProcess%3A1%3A4/identitylinks/users/kermit",
      "user":"kermit",
      "group":null,
      "type":"candidate"
   }
]

EOF
        ],
        [
            <<<EOF
            Add a candidate starter to a process definition
EOF
            , 'POST repository/process-definitions/{processDefinitionId}/identitylinks', 'POST', 'repository/process-definitions/{processDefinitionId}/identitylinks',
            <<<EOF

Table 15.36. Add a candidate starter to a process definition - URL parameters
Parameter	Required	Value	Description
processDefinitionId	Yes	String	The id of the process definition.

Request body (user):

{
  "user" : "kermit"
}
Request body (group):

{
  "groupId" : "sales"
}
Table 15.37. Add a candidate starter to a process definition - Response codes
Response code	Description
201	Indicates the process definition was found and the identity link was created.
404	Indicates the requested process definition was not found.

Success response body:

{
  "url":"http://localhost:8182/repository/process-definitions/oneTaskProcess%3A1%3A4/identitylinks/users/kermit",
  "user":"kermit",
  "group":null,
  "type":"candidate"
}

EOF
        ],
        [
            <<<EOF
            Delete a candidate starter from a process definition
EOF
            , 'DELETE repository/process-definitions/{processDefinitionId}/identitylinks/{family}/{identityId}', 'DELETE', 'repository/process-definitions/{processDefinitionId}/identitylinks/{family}/{identityId}',
            <<<EOF

Table 15.38. Delete a candidate starter from a process definition - URL parameters
Parameter	Required	Value	Description
processDefinitionId	Yes	String	The id of the process definition.
family	Yes	String	Either users or groups, depending on the type of identity link.
identityId	Yes	String	Either the userId or groupId of the identity to remove as candidate starter.

Table 15.39. Delete a candidate starter from a process definition - Response codes
Response code	Description
204	Indicates the process definition was found and the identity link was removed. The response body is intentionally empty.
404	Indicates the requested process definition was not found or the process definition doesn't have an identity-link that matches the url.

Success response body:

{
  "url":"http://localhost:8182/repository/process-definitions/oneTaskProcess%3A1%3A4/identitylinks/users/kermit",
  "user":"kermit",
  "group":null,
  "type":"candidate"
}

EOF
        ],
        [
            <<<EOF
            Get a candidate starter from a process definition
EOF
            , 'GET repository/process-definitions/{processDefinitionId}/identitylinks/{family}/{identityId}', 'GET', 'repository/process-definitions/{processDefinitionId}/identitylinks/{family}/{identityId}',
            <<<EOF

Table 15.40. Get a candidate starter from a process definition - URL parameters
Parameter	Required	Value	Description
processDefinitionId	Yes	String	The id of the process definition.
family	Yes	String	Either users or groups, depending on the type of identity link.
identityId	Yes	String	Either the userId or groupId of the identity to get as candidate starter.

Table 15.41. Get a candidate starter from a process definition - Response codes
Response code	Description
200	Indicates the process definition was found and the identity link was returned.
404	Indicates the requested process definition was not found or the process definition doesn't have an identity-link that matches the url.

Success response body:

{
  "url":"http://localhost:8182/repository/process-definitions/oneTaskProcess%3A1%3A4/identitylinks/users/kermit",
  "user":"kermit",
  "group":null,
  "type":"candidate"
}
Models

EOF
        ],
        [
            <<<EOF
            Get a list of models
EOF
            , 'GET repository/models', 'GET', 'repository/models',
            <<<EOF

Table 15.42. Get a list of models - URL query parameters
Parameter	Required	Value	Description
id	No	String	Only return models with the given id.
category	No	String	Only return models with the given category.
categoryLike	No	String	Only return models with a category like the given value. Use the % character as wildcard.
categoryNotEquals	No	String	Only return models without the given category.
name	No	String	Only return models with the given name.
nameLike	No	String	Only return models with a name like the given value. Use the % character as wildcard.
key	No	String	Only return models with the given key.
deploymentId	No	String	Only return models which are deployed in the given deployment.
version	No	Integer	Only return models with the given version.
latestVersion	No	Boolean	If true, only return models which are the latest version. Best used in combination with key. If false is passed in as value, this is ignored and all versions are returned.
deployed	No	Boolean	If true, only deployed models are returned. If false, only undeployed models are returned (deploymentId is null).
tenantId	No	String	Only return models with the given tenantId.
tenantIdLike	No	String	Only return models with a tenantId like the given value.
withoutTenantId	No	Boolean	If true, only returns models without a tenantId set. If false, the withoutTenantId parameter is ignored.
sort	No	'id' (default), 'category', 'createTime', 'key', 'lastUpdateTime', 'name', 'version' or 'tenantId'	Property to sort on, to be used together with the 'order'.
The general paging and sorting query-parameters can be used for this URL.



Table 15.43. Get a list of models - Response codes
Response code	Description
200	Indicates request was successful and the models are returned
400	Indicates a parameter was passed in the wrong format. The status-message contains additional information.

Success response body:

{
   "data":[
      {
         "name":"Model name",
         "key":"Model key",
         "category":"Model category",
         "version":2,
         "metaInfo":"Model metainfo",
         "deploymentId":"7",
         "id":"10",
         "url":"http://localhost:8182/repository/models/10",
         "createTime":"2013-06-12T14:31:08.612+0000",
         "lastUpdateTime":"2013-06-12T14:31:08.612+0000",
         "deploymentUrl":"http://localhost:8182/repository/deployments/7",
         "tenantId":null
      },

      ...

   ],
   "total":2,
   "start":0,
   "sort":"id",
   "order":"asc",
   "size":2
}

EOF
        ],
        [
            <<<EOF
            Get a model
EOF
            , 'GET repository/models/{modelId}', 'GET', 'repository/models/{modelId}',
            <<<EOF

Table 15.44. Get a model - URL parameters
Parameter	Required	Value	Description
modelId	Yes	String	The id of the model to get.

Table 15.45. Get a model - Response codes
Response code	Description
200	Indicates the model was found and returned.
404	Indicates the requested model was not found.

Success response body:

{
   "id":"5",
   "url":"http://localhost:8182/repository/models/5",
   "name":"Model name",
   "key":"Model key",
   "category":"Model category",
   "version":2,
   "metaInfo":"Model metainfo",
   "deploymentId":"2",
   "deploymentUrl":"http://localhost:8182/repository/deployments/2",
   "createTime":"2013-06-12T12:31:19.861+0000",
   "lastUpdateTime":"2013-06-12T12:31:19.861+0000",
   "tenantId":null
}

EOF
        ],
        [
            <<<EOF
            Update a model
EOF
            , 'PUT repository/models/{modelId}', 'PUT', 'repository/models/{modelId}',
            <<<EOF

Request body:

{
   "name":"Model name",
   "key":"Model key",
   "category":"Model category",
   "version":2,
   "metaInfo":"Model metainfo",
   "deploymentId":"2",
   "tenantId":"updatedTenant"
}
All request values are optional. For example, you can only include the 'name' attribute in the request body JSON-object, only updating the name of the model, leaving all other fields unaffected. When an attribute is explicitly included and is set to null, the model-value will be updated to null. Example: {"metaInfo" : null} will clear the metaInfo of the model).

Table 15.46. Update a model - Response codes
Response code	Description
200	Indicates the model was found and updated.
404	Indicates the requested model was not found.

Success response body:

{
   "id":"5",
   "url":"http://localhost:8182/repository/models/5",
   "name":"Model name",
   "key":"Model key",
   "category":"Model category",
   "version":2,
   "metaInfo":"Model metainfo",
   "deploymentId":"2",
   "deploymentUrl":"http://localhost:8182/repository/deployments/2",
   "createTime":"2013-06-12T12:31:19.861+0000",
   "lastUpdateTime":"2013-06-12T12:31:19.861+0000",
   "tenantId":""updatedTenant"
}

EOF
        ],
        [
            <<<EOF
            Create a model
EOF
            , 'POST repository/models', 'POST', 'repository/models',
            <<<EOF

Request body:

{
   "name":"Model name",
   "key":"Model key",
   "category":"Model category",
   "version":1,
   "metaInfo":"Model metainfo",
   "deploymentId":"2",
   "tenantId":"tenant""
}
All request values are optional. For example, you can only include the 'name' attribute in the request body JSON-object, only setting the name of the model, leaving all other fields null.

Table 15.47. Create a model - Response codes
Response code	Description
201	Indicates the model was created.

Success response body:

{
   "id":"5",
   "url":"http://localhost:8182/repository/models/5",
   "name":"Model name",
   "key":"Model key",
   "category":"Model category",
   "version":1,
   "metaInfo":"Model metainfo",
   "deploymentId":"2",
   "deploymentUrl":"http://localhost:8182/repository/deployments/2",
   "createTime":"2013-06-12T12:31:19.861+0000",
   "lastUpdateTime":"2013-06-12T12:31:19.861+0000",
   "tenantId":"tenant"
}

EOF
        ],
        [
            <<<EOF
            Delete a model
EOF
            , 'DELETE repository/models/{modelId}', 'DELETE', 'repository/models/{modelId}',
            <<<EOF

Table 15.48. Delete a model - URL parameters
Parameter	Required	Value	Description
modelId	Yes	String	The id of the model to delete.

Table 15.49. Delete a model - Response codes
Response code	Description
204	Indicates the model was found and has been deleted. Response-body is intentionally empty.
404	Indicates the requested model was not found.


EOF
        ],
        [
            <<<EOF
            Get the editor source for a model
EOF
            , 'GET repository/models/{modelId}/source', 'GET', 'repository/models/{modelId}/source',
            <<<EOF

Table 15.50. Get the editor source for a model - URL parameters
Parameter	Required	Value	Description
modelId	Yes	String	The id of the model.

Table 15.51. Get the editor source for a model - Response codes
Response code	Description
200	Indicates the model was found and source is returned.
404	Indicates the requested model was not found.

Success response body: Response body contains the model's raw editor source. The response's content-type is set to application/octet-stream, regardless of the content of the source.


EOF
        ],
        [
            <<<EOF
            Set the editor source for a model
EOF
            , 'PUT repository/models/{modelId}/source', 'PUT', 'repository/models/{modelId}/source',
            <<<EOF

Table 15.52. Set the editor source for a model - URL parameters
Parameter	Required	Value	Description
modelId	Yes	String	The id of the model.

Request body: The request should be of type multipart/form-data. There should be a single file-part included with the binary value of the source.

Table 15.53. Set the editor source for a model - Response codes
Response code	Description
200	Indicates the model was found and the source has been updated.
404	Indicates the requested model was not found.

Success response body: Response body contains the model's raw editor source. The response's content-type is set to application/octet-stream, regardless of the content of the source.


EOF
        ],
        [
            <<<EOF
            Get the extra editor source for a model
EOF
            , 'GET repository/models/{modelId}/source-extra', 'GET', 'repository/models/{modelId}/source-extra',
            <<<EOF

Table 15.54. Get the extra editor source for a model - URL parameters
Parameter	Required	Value	Description
modelId	Yes	String	The id of the model.

Table 15.55. Get the extra editor source for a model - Response codes
Response code	Description
200	Indicates the model was found and source is returned.
404	Indicates the requested model was not found.

Success response body: Response body contains the model's raw extra editor source. The response's content-type is set to application/octet-stream, regardless of the content of the extra source.


EOF
        ],
        [
            <<<EOF
            Set the extra editor source for a model
EOF
            , 'PUT repository/models/{modelId}/source-extra', 'PUT', 'repository/models/{modelId}/source-extra',
            <<<EOF

Table 15.56. Set the extra editor source for a model - URL parameters
Parameter	Required	Value	Description
modelId	Yes	String	The id of the model.

Request body: The request should be of type multipart/form-data. There should be a single file-part included with the binary value of the extra source.

Table 15.57. Set the extra editor source for a model - Response codes
Response code	Description
200	Indicates the model was found and the extra source has been updated.
404	Indicates the requested model was not found.

Success response body: Response body contains the model's raw editor source. The response's content-type is set to application/octet-stream, regardless of the content of the source.

Process Instances

EOF
        ],
        [
            <<<EOF
            Get a process instance
EOF
            , 'GET runtime/process-instances/{processInstanceId}', 'GET', 'runtime/process-instances/{processInstanceId}',
            <<<EOF

Table 15.58. Get a process instance - URL parameters
Parameter	Required	Value	Description
processInstanceId	Yes	String	The id of the process instance to get.

Table 15.59. Get a process instance - Response codes
Response code	Description
200	Indicates the process instance was found and returned.
404	Indicates the requested process instance was not found.

Success response body:

{
   "id":"7",
   "url":"http://localhost:8182/runtime/process-instances/7",
   "businessKey":"myBusinessKey",
   "suspended":false,
   "processDefinitionUrl":"http://localhost:8182/repository/process-definitions/processOne%3A1%3A4",
   "activityId":"processTask",
   "tenantId": null
}

EOF
        ],
        [
            <<<EOF
            Delete a process instance
EOF
            , 'DELETE runtime/process-instances/{processInstanceId}', 'DELETE', 'runtime/process-instances/{processInstanceId}',
            <<<EOF

Table 15.60. Delete a process instance - URL parameters
Parameter	Required	Value	Description
processInstanceId	Yes	String	The id of the process instance to delete.

Table 15.61. Delete a process instance - Response codes
Response code	Description
204	Indicates the process instance was found and deleted. Response body is left empty intentionally.
404	Indicates the requested process instance was not found.


EOF
        ],
        [
            <<<EOF
            Activate or suspend a process instance
EOF
            , 'PUT runtime/process-instances/{processInstanceId}', 'PUT', 'runtime/process-instances/{processInstanceId}',
            <<<EOF

Table 15.62. Activate or suspend a process instance - URL parameters
Parameter	Required	Value	Description
processInstanceId	Yes	String	The id of the process instance to activate/suspend.

Request response body (suspend):

{
   "action":"suspend"
}
Request response body (activate):

{
   "action":"activate"
}
Table 15.63. Activate or suspend a process instance - Response codes
Response code	Description
200	Indicates the process instance was found and action was executed.
400	Indicates an invalid action was supplied.
404	Indicates the requested process instance was not found.
409	Indicates the requested process instance action cannot be executed since the process-instance is already activated/suspended.


EOF
        ],
        [
            <<<EOF
            Start a process instance
EOF
            , 'POST runtime/process-instances', 'POST', 'runtime/process-instances',
            <<<EOF

Request body (start by process definition id):

{
   "processDefinitionId":"oneTaskProcess:1:158",
   "businessKey":"myBusinessKey",
   "variables": [
      {
        "name":"myVar",
        "value":"This is a variable",
      },

      ...
   ]
}
Request body (start by process definition key):

{
   "processDefinitionKey":"oneTaskProcess",
   "businessKey":"myBusinessKey",
   "tenantId": "tenant1",
   "variables": [
      {
        "name":"myVar",
        "value":"This is a variable",
      },

      ...
   ]
}
Request body (start by message):

{
   "message":"newOrderMessage",
   "businessKey":"myBusinessKey",
   "tenantId": "tenant1",
   "variables": [
      {
        "name":"myVar",
        "value":"This is a variable",
      },

      ...
   ]
}
Only one of processDefinitionId, processDefinitionKey or message can be used in the request body. Parameters businessKey, variables and tenantId are optional. If tenantId is omitted, the default tenant will be used. More information about the variable format can be found in the REST variables section. Note that the variable-scope that is supplied is ignored, process-variables are always local.

Table 15.64. Start a process instance - Response codes
Response code	Description
201	Indicates the process instance was created.
400	Indicates either the process-definition was not found (based on id or key), no process is started by sending the given message or an invalid variable has been passed. Status description contains additional information about the error.

Success response body:

{
   "id":"7",
   "url":"http://localhost:8182/runtime/process-instances/7",
   "businessKey":"myBusinessKey",
   "suspended":false,
   "processDefinitionUrl":"http://localhost:8182/repository/process-definitions/processOne%3A1%3A4",
   "activityId":"processTask",
   "tenantId" : null
}

EOF
        ],
        [
            <<<EOF
            List of process instances
EOF
            , 'GET runtime/process-instances', 'GET', 'runtime/process-instances',
            <<<EOF

Table 15.65. List of process instances - URL query parameters
Parameter	Required	Value	Description
id	No	String	Only return process instance with the given id.
processDefinitionKey	No	String	Only return process instances with the given process definition key.
processDefinitionId	No	String	Only return process instances with the given process definition id.
businessKey	No	String	Only return process instances with the given businessKey.
involvedUser	No	String	Only return process instances in which the given user is involved.
suspended	No	Boolean	If true, only return process instance which are suspended. If false, only return process instances which are not suspended (active).
superProcessInstanceId	No	String	Only return process instances which have the given super process-instance id (for processes that have a call-activities).
subProcessInstanceId	No	String	Only return process instances which have the given sub process-instance id (for processes started as a call-activity).
excludeSubprocesses	No	Boolean	Return only process instances which aren't sub processes.
includeProcessVariables	No	Boolean	Indication to include process variables in the result.
tenantId	No	String	Only return process instances with the given tenantId.
tenantIdLike	No	String	Only return process instances with a tenantId like the given value.
withoutTenantId	No	Boolean	If true, only returns process instances without a tenantId set. If false, the withoutTenantId parameter is ignored.
sort	No	String	Sort field, should be either one of id (default), processDefinitionId, tenantId or processDefinitionKey.
The general paging and sorting query-parameters can be used for this URL.



Table 15.66. List of process instances - Response codes
Response code	Description
200	Indicates request was successful and the process-instances are returned
400	Indicates a parameter was passed in the wrong format . The status-message contains additional information.

Success response body:

{
   "data":[
      {
         "id":"7",
         "url":"http://localhost:8182/runtime/process-instances/7",
         "businessKey":"myBusinessKey",
         "suspended":false,
         "processDefinitionUrl":"http://localhost:8182/repository/process-definitions/processOne%3A1%3A4",
         "activityId":"processTask",
         "tenantId" : null
      },

      ...
   ],
   "total":2,
   "start":0,
   "sort":"id",
   "order":"asc",
   "size":2
}

EOF
        ],
        [
            <<<EOF
            Query process instances
EOF
            , 'POST query/process-instances', 'POST', 'query/process-instances',
            <<<EOF

Request body:

{
  "processDefinitionKey":"oneTaskProcess",
  "variables":
  [
    {
        "name" : "myVariable",
        "value" : 1234,
        "operation" : "equals",
        "type" : "long"
    },
    ...
  ],
  ...
}
The request body can contain all possible filters that can be used in the List process instances URL query. On top of these, it's possible to provide an array of variables to include in the query, with their format described here.

The general paging and sorting query-parameters can be used for this URL.

Table 15.67. Query process instances - Response codes
Response code	Description
200	Indicates request was successful and the process-instances are returned
400	Indicates a parameter was passed in the wrong format . The status-message contains additional information.

Success response body:

{
   "data":[
      {
         "id":"7",
         "url":"http://localhost:8182/runtime/process-instances/7",
         "businessKey":"myBusinessKey",
         "suspended":false,
         "processDefinitionUrl":"http://localhost:8182/repository/process-definitions/processOne%3A1%3A4",
         "activityId":"processTask",
         "tenantId" : null
      },

      ...
   ],
   "total":2,
   "start":0,
   "sort":"id",
   "order":"asc",
   "size":2
}

EOF
        ],
        [
            <<<EOF
            Get diagram for a process instance
EOF
            , 'GET runtime/process-instances/{processInstanceId}/diagram', 'GET', 'runtime/process-instances/{processInstanceId}/diagram',
            <<<EOF

Table 15.68. Get diagram for a process instance - URL parameters
Parameter	Required	Value	Description
processInstanceId	Yes	String	The id of the process instance to get the diagram for.

Table 15.69. Get diagram for a process instance - Response codes
Response code	Description
200	Indicates the process instance was found and the diagram was returned.
400	Indicates the requested process instance was not found but the process doesn't contain any graphical information (BPMN:DI) and no diagram can be created.
404	Indicates the requested process instance was not found.

Success response body:

{
   "id":"7",
   "url":"http://localhost:8182/runtime/process-instances/7",
   "businessKey":"myBusinessKey",
   "suspended":false,
   "processDefinitionUrl":"http://localhost:8182/repository/process-definitions/processOne%3A1%3A4",
   "activityId":"processTask"
}

EOF
        ],
        [
            <<<EOF
            Get involved people for process instance
EOF
            , 'GET runtime/process-instances/{processInstanceId}/identitylinks', 'GET', 'runtime/process-instances/{processInstanceId}/identitylinks',
            <<<EOF

Table 15.70. Get involved people for process instance - URL parameters
Parameter	Required	Value	Description
processInstanceId	Yes	String	The id of the process instance to the links for.

Table 15.71. Get involved people for process instance - Response codes
Response code	Description
200	Indicates the process instance was found and links are returned.
404	Indicates the requested process instance was not found.

Success response body:

[
   {
      "url":"http://localhost:8182/runtime/process-instances/5/identitylinks/users/john/customType",
      "user":"john",
      "group":null,
      "type":"customType"
   },
   {
      "url":"http://localhost:8182/runtime/process-instances/5/identitylinks/users/paul/candidate",
      "user":"paul",
      "group":null,
      "type":"candidate"
   }
]
Note that the groupId will always be null, as it's only possible to involve users with a process-instance.


EOF
        ],
        [
            <<<EOF
            Add an involved user to a process instance
EOF
            , 'POST runtime/process-instances/{processInstanceId}/identitylinks', 'POST', 'runtime/process-instances/{processInstanceId}/identitylinks',
            <<<EOF

Table 15.72. Add an involved user to a process instance - URL parameters
Parameter	Required	Value	Description
processInstanceId	Yes	String	The id of the process instance to the links for.

Request body:

{
  "userId":"kermit",
  "type":"participant"
}
Both userId and type are required.

Table 15.73. Add an involved user to a process instance - Response codes
Response code	Description
201	Indicates the process instance was found and the link is created.
400	Indicates the requested body did not contain a userId or a type.
404	Indicates the requested process instance was not found.

Success response body:

{
   "url":"http://localhost:8182/runtime/process-instances/5/identitylinks/users/john/customType",
   "user":"john",
   "group":null,
   "type":"customType"
}
Note that the groupId will always be null, as it's only possible to involve users with a process-instance.


EOF
        ],
        [
            <<<EOF
            Remove an involved user to from process instance
EOF
            , 'DELETE runtime/process-instances/{processInstanceId}/identitylinks/users/{userId}/{type}', 'DELETE', 'runtime/process-instances/{processInstanceId}/identitylinks/users/{userId}/{type}',
            <<<EOF

Table 15.74. Remove an involved user to from process instance - URL parameters
Parameter	Required	Value	Description
processInstanceId	Yes	String	The id of the process instance.
userId	Yes	String	The id of the user to delete link for.
type	Yes	String	Type of link to delete.

Table 15.75. Remove an involved user to from process instance - Response codes
Response code	Description
204	Indicates the process instance was found and the link has been deleted. Response body is left empty intentionally.
404	Indicates the requested process instance was not found or the link to delete doesn't exist. The response status contains additional information about the error.

Success response body:

{
   "url":"http://localhost:8182/runtime/process-instances/5/identitylinks/users/john/customType",
   "user":"john",
   "group":null,
   "type":"customType"
}
Note that the groupId will always be null, as it's only possible to involve users with a process-instance.


EOF
        ],
        [
            <<<EOF
            List of variables for a process instance
EOF
            , 'GET runtime/process-instances/{processInstanceId}/variables', 'GET', 'runtime/process-instances/{processInstanceId}/variables',
            <<<EOF

Table 15.76. List of variables for a process instance - URL parameters
Parameter	Required	Value	Description
processInstanceId	Yes	String	The id of the process instance to the variables for.

Table 15.77. List of variables for a process instance - Response codes
Response code	Description
200	Indicates the process instance was found and variables are returned.
404	Indicates the requested process instance was not found.

Success response body:

[
   {
      "name":"intProcVar",
      "type":"integer",
      "value":123,
      "scope":"local"
   },
   {
      "name":"byteArrayProcVar",
      "type":"binary",
      "value":null,
      "valueUrl":"http://localhost:8182/runtime/process-instances/5/variables/byteArrayProcVar/data",
      "scope":"local"
   },

   ...
]
In case the variable is a binary variable or serializable, the valueUrl points to an URL to fetch the raw value. If it's a plain variable, the value is present in the response. Note that only local scoped variables are returned, as there is no global scope for process-instance variables.


EOF
        ],
        [
            <<<EOF
            Get a variable for a process instance
EOF
            , 'GET runtime/process-instances/{processInstanceId}/variables/{variableName}', 'GET', 'runtime/process-instances/{processInstanceId}/variables/{variableName}',
            <<<EOF

Table 15.78. Get a variable for a process instance - URL parameters
Parameter	Required	Value	Description
processInstanceId	Yes	String	The id of the process instance to the variables for.
variableName	Yes	String	Name of the variable to get.

Table 15.79. Get a variable for a process instance - Response codes
Response code	Description
200	Indicates both the process instance and variable were found and variable is returned.
400	Indicates the request body is incomplete or contains illegal values. The status description contains additional information about the error.
404	Indicates the requested process instance was not found or the process instance does not have a variable with the given name. Status description contains additional information about the error.

Success response body:

   {
      "name":"intProcVar",
      "type":"integer",
      "value":123,
      "scope":"local"
   }
In case the variable is a binary variable or serializable, the valueUrl points to an URL to fetch the raw value. If it's a plain variable, the value is present in the response. Note that only local scoped variables are returned, as there is no global scope for process-instance variables.


EOF
        ],
        [
            <<<EOF
            Create (or update) variables on a process instance
EOF
            , 'POST runtime/process-instances/{processInstanceId}/variables', 'POST', 'runtime/process-instances/{processInstanceId}/variables',
            <<<EOF

PUT runtime/process-instances/{processInstanceId}/variables
When using POST, all variables that are passed are created. In case one of the variables already exists on the process instance, the request results in an error (409 - CONFLICT). When PUT is used, unexisting variables are created on the process-instance and existing ones are overridden without any error.

Table 15.80. Create (or update) variables on a process instance - URL parameters
Parameter	Required	Value	Description
processInstanceId	Yes	String	The id of the process instance to the variables for.

Request body:

[
   {
      "name":"intProcVar"
      "type":"integer"
      "value":123
   },

   ...
]
Any number of variables can be passed into the request body array. More information about the variable format can be found in the REST variables section. Note that scope is ignored, only local variables can be set in a process instance.

Table 15.81. Create (or update) variables on a process instance - Response codes
Response code	Description
201	Indicates the process instance was found and variable is created.
400	Indicates the request body is incomplete or contains illegal values. The status description contains additional information about the error.
404	Indicates the requested process instance was not found.
409	Indicates the process instance was found but already contains a variable with the given name (only thrown when POST method is used). Use the update-method instead.

Success response body:

[
   {
      "name":"intProcVar",
      "type":"integer",
      "value":123,
      "scope":"local"
   },

   ...

]

EOF
        ],
        [
            <<<EOF
            Update a single variable on a process instance
EOF
            , 'PUT runtime/process-instances/{processInstanceId}/variables/{variableName}', 'PUT', 'runtime/process-instances/{processInstanceId}/variables/{variableName}',
            <<<EOF

Table 15.82. Update a single variable on a process instance - URL parameters
Parameter	Required	Value	Description
processInstanceId	Yes	String	The id of the process instance to the variables for.
variableName	Yes	String	Name of the variable to get.

Request body:

 {
    "name":"intProcVar"
    "type":"integer"
    "value":123
 }
More information about the variable format can be found in the REST variables section. Note that scope is ignored, only local variables can be set in a process instance.

Table 15.83. Update a single variable on a process instance - Response codes
Response code	Description
200	Indicates both the process instance and variable were found and variable is updated.
404	Indicates the requested process instance was not found or the process instance does not have a variable with the given name. Status description contains additional information about the error.

Success response body:

   {
      "name":"intProcVar",
      "type":"integer",
      "value":123,
      "scope":"local"
   }
In case the variable is a binary variable or serializable, the valueUrl points to an URL to fetch the raw value. If it's a plain variable, the value is present in the response. Note that only local scoped variables are returned, as there is no global scope for process-instance variables.


EOF
        ],
        [
            <<<EOF
            Create a new binary variable on a process-instance
EOF
            , 'POST runtime/process-instances/{processInstanceId}/variables', 'POST', 'runtime/process-instances/{processInstanceId}/variables',
            <<<EOF

Table 15.84. Create a new binary variable on a process-instance - URL parameters
Parameter	Required	Value	Description
processInstanceId	Yes	String	The id of the process instance to create the new variable for.

Request body: The request should be of type multipart/form-data. There should be a single file-part included with the binary value of the variable. On top of that, the following additional form-fields can be present:

name: Required name of the variable.
type: Type of variable that is created. If omitted, binary is assumed and the binary data in the request will be stored as an array of bytes.
Success response body:

{
  "name" : "binaryVariable",
  "scope" : "local",
  "type" : "binary",
  "value" : null,
  "valueUrl" : "http://.../runtime/process-instances/123/variables/binaryVariable/data"
}
Table 15.85. Create a new binary variable on a process-instance - Response codes
Response code	Description
201	Indicates the variable was created and the result is returned.
400	Indicates the name of the variable to create was missing. Status message provides additional information.
404	Indicates the requested process instance was not found.
409	Indicates the process instance already has a variable with the given name. Use the PUT method to update the task variable instead.
415	Indicates the serializable data contains an object for which no class is present in the JVM running the Activiti engine and therefore cannot be deserialized.


EOF
        ],
        [
            <<<EOF
            Update an existing binary variable on a process-instance
EOF
            , 'PUT runtime/process-instances/{processInstanceId}/variables', 'PUT', 'runtime/process-instances/{processInstanceId}/variables',
            <<<EOF

Table 15.86. Update an existing binary variable on a process-instance - URL parameters
Parameter	Required	Value	Description
processInstanceId	Yes	String	The id of the process instance to create the new variable for.

Request body: The request should be of type multipart/form-data. There should be a single file-part included with the binary value of the variable. On top of that, the following additional form-fields can be present:

name: Required name of the variable.
type: Type of variable that is created. If omitted, binary is assumed and the binary data in the request will be stored as an array of bytes.
Success response body:

{
  "name" : "binaryVariable",
  "scope" : "local",
  "type" : "binary",
  "value" : null,
  "valueUrl" : "http://.../runtime/process-instances/123/variables/binaryVariable/data"
}
Table 15.87. Update an existing binary variable on a process-instance - Response codes
Response code	Description
200	Indicates the variable was updated and the result is returned.
400	Indicates the name of the variable to update was missing. Status message provides additional information.
404	Indicates the requested process instance was not found or the process instance does not have a variable with the given name.
415	Indicates the serializable data contains an object for which no class is present in the JVM running the Activiti engine and therefore cannot be deserialized.

Executions

EOF
        ],
        [
            <<<EOF
            Get an execution
EOF
            , 'GET runtime/executions/{executionId}', 'GET', 'runtime/executions/{executionId}',
            <<<EOF

Table 15.88. Get an execution - URL parameters
Parameter	Required	Value	Description
executionId	Yes	String	The id of the execution to get.

Table 15.89. Get an execution - Response codes
Response code	Description
200	Indicates the execution was found and returned.
404	Indicates the execution was not found.

Success response body:

{
   "id":"5",
   "url":"http://localhost:8182/runtime/executions/5",
   "parentId":null,
   "parentUrl":null,
   "processInstanceId":"5",
   "processInstanceUrl":"http://localhost:8182/runtime/process-instances/5",
   "suspended":false,
   "activityId":null,
   "tenantId": null
}

EOF
        ],
        [
            <<<EOF
            Execute an action on an execution
EOF
            , 'PUT runtime/executions/{executionId}', 'PUT', 'runtime/executions/{executionId}',
            <<<EOF

Table 15.90. Execute an action on an execution - URL parameters
Parameter	Required	Value	Description
executionId	Yes	String	The id of the execution to execute action on.

Request body (signal an execution):

{
  "action":"signal"
}
Request body (signal event received for execution):

{
  "action":"signalEventReceived",
  "signalName":"mySignal"
  "variables": [ ... ]
}
Notifies the execution that a signal event has been received, requires a signalName parameter. Optional variables can be passed that are set on the execution before the action is executed.

Request body (signal event received for execution):

{
  "action":"messageEventReceived",
  "messageName":"myMessage"
  "variables": [ ... ]
}
Notifies the execution that a message event has been received, requires a messageName parameter. Optional variables can be passed that are set on the execution before the action is executed.

Table 15.91. Execute an action on an execution - Response codes
Response code	Description
200	Indicates the execution was found and the action is performed.
204	Indicates the execution was found, the action was performed and the action caused the execution to end.
400	Indicates an illegal action was requested, required parameters are missing in the request body or illegal variables are passed in. Status description contains additional information about the error.
404	Indicates the execution was not found.

Success response body (in case execution is not ended due to action):

{
   "id":"5",
   "url":"http://localhost:8182/runtime/executions/5",
   "parentId":null,
   "parentUrl":null,
   "processInstanceId":"5",
   "processInstanceUrl":"http://localhost:8182/runtime/process-instances/5",
   "suspended":false,
   "activityId":null,
   "tenantId" : null
}

EOF
        ],
        [
            <<<EOF
            Get active activities in an execution
EOF
            , 'GET runtime/executions/{executionId}/activities', 'GET', 'runtime/executions/{executionId}/activities',
            <<<EOF

Returns all activities which are active in the execution and in all child-executions (and their children, recursively), if any.

Table 15.92. Get active activities in an execution - URL parameters
Parameter	Required	Value	Description
executionId	Yes	String	The id of the execution to get activities for.

Table 15.93. Get active activities in an execution - Response codes
Response code	Description
200	Indicates the execution was found and activities are returned.
404	Indicates the execution was not found.

Success response body:

[
  "userTaskForManager",
  "receiveTask"
]

EOF
        ],
        [
            <<<EOF
            List of executions
EOF
            , 'GET runtime/executions', 'GET', 'runtime/executions',
            <<<EOF

Table 15.94. List of executions - URL query parameters
Parameter	Required	Value	Description
id	No	String	Only return executions with the given id.
activityId	No	String	Only return executions with the given activity id.
processDefinitionKey	No	String	Only return executions with the given process definition key.
processDefinitionId	No	String	Only return executions with the given process definition id.
processInstanceId	No	String	Only return executions which are part of the process instance with the given id.
messageEventSubscriptionName	No	String	Only return executions which are subscribed to a message with the given name.
signalEventSubscriptionName	No	String	Only return executions which are subscribed to a signal with the given name.
parentId	No	String	Only return executions which are a direct child of the given execution.
tenantId	No	String	Only return executions with the given tenantId.
tenantIdLike	No	String	Only return executions with a tenantId like the given value.
withoutTenantId	No	Boolean	If true, only returns executions without a tenantId set. If false, the withoutTenantId parameter is ignored.
sort	No	String	Sort field, should be either one of processInstanceId (default), processDefinitionId, processDefinitionKey or tenantId.
The general paging and sorting query-parameters can be used for this URL.



Table 15.95. List of executions - Response codes
Response code	Description
200	Indicates request was successful and the executions are returned
400	Indicates a parameter was passed in the wrong format . The status-message contains additional information.

Success response body:

{
   "data":[
      {
         "id":"5",
         "url":"http://localhost:8182/runtime/executions/5",
         "parentId":null,
         "parentUrl":null,
         "processInstanceId":"5",
         "processInstanceUrl":"http://localhost:8182/runtime/process-instances/5",
         "suspended":false,
         "activityId":null,
         "tenantId":null
      },
      {
         "id":"7",
         "url":"http://localhost:8182/runtime/executions/7",
         "parentId":"5",
         "parentUrl":"http://localhost:8182/runtime/executions/5",
         "processInstanceId":"5",
         "processInstanceUrl":"http://localhost:8182/runtime/process-instances/5",
         "suspended":false,
         "activityId":"processTask",
         "tenantId":null
      }
   ],
   "total":2,
   "start":0,
   "sort":"processInstanceId",
   "order":"asc",
   "size":2
}

EOF
        ],
        [
            <<<EOF
            Query executions
EOF
            , 'POST query/executions', 'POST', 'query/executions',
            <<<EOF

Request body:

{
  "processDefinitionKey":"oneTaskProcess",
  "variables":
  [
    {
        "name" : "myVariable",
        "value" : 1234,
        "operation" : "equals",
        "type" : "long"
    },
    ...
  ],
  "processInstanceVariables":
  [
    {
        "name" : "processVariable",
        "value" : "some string",
        "operation" : "equals",
        "type" : "string"
    },
    ...
  ],
  ...
}
The request body can contain all possible filters that can be used in the List executions URL query. On top of these, it's possible to provide an array of variables and processInstanceVariables to include in the query, with their format described here.

The general paging and sorting query-parameters can be used for this URL.

Table 15.96. Query executions - Response codes
Response code	Description
200	Indicates request was successful and the executions are returned
400	Indicates a parameter was passed in the wrong format . The status-message contains additional information.

Success response body:

{
   "data":[
      {
         "id":"5",
         "url":"http://localhost:8182/runtime/executions/5",
         "parentId":null,
         "parentUrl":null,
         "processInstanceId":"5",
         "processInstanceUrl":"http://localhost:8182/runtime/process-instances/5",
         "suspended":false,
         "activityId":null,
         "tenantId":null
      },
      {
         "id":"7",
         "url":"http://localhost:8182/runtime/executions/7",
         "parentId":"5",
         "parentUrl":"http://localhost:8182/runtime/executions/5",
         "processInstanceId":"5",
         "processInstanceUrl":"http://localhost:8182/runtime/process-instances/5",
         "suspended":false,
         "activityId":"processTask",
         "tenantId":null
      }
   ],
   "total":2,
   "start":0,
   "sort":"processInstanceId",
   "order":"asc",
   "size":2
}

EOF
        ],
        [
            <<<EOF
            List of variables for an execution
EOF
            , 'GET runtime/executions/{executionId}/variables?scope={scope}', 'GET', 'runtime/executions/{executionId}/variables?scope={scope}',
            <<<EOF

Table 15.97. List of variables for an execution - URL parameters
Parameter	Required	Value	Description
executionId	Yes	String	The id of the execution to the variables for.
scope	No	String	Either local or global. If omitted, both local and global scoped variables are returned.

Table 15.98. List of variables for an execution - Response codes
Response code	Description
200	Indicates the execution was found and variables are returned.
404	Indicates the requested execution was not found.

Success response body:

[
   {
      "name":"intProcVar",
      "type":"integer",
      "value":123,
      "scope":"global"
   },
   {
      "name":"byteArrayProcVar",
      "type":"binary",
      "value":null,
      "valueUrl":"http://localhost:8182/runtime/process-instances/5/variables/byteArrayProcVar/data",
      "scope":"local"
   },

   ...
]
In case the variable is a binary variable or serializable, the valueUrl points to an URL to fetch the raw value. If it's a plain variable, the value is present in the response.


EOF
        ],
        [
            <<<EOF
            Get a variable for an execution
EOF
            , 'GET runtime/executions/{executionId}/variables/{variableName}?scope={scope}', 'GET', 'runtime/executions/{executionId}/variables/{variableName}?scope={scope}',
            <<<EOF

Table 15.99. Get a variable for an execution - URL parameters
Parameter	Required	Value	Description
executionId	Yes	String	The id of the execution to the variables for.
variableName	Yes	String	Name of the variable to get.
scope	No	String	Either local or global. If omitted, local variable is returned (if exists). If not, a global variable is returned (if exists).

Table 15.100. Get a variable for an execution - Response codes
Response code	Description
200	Indicates both the execution and variable were found and variable is returned.
400	Indicates the request body is incomplete or contains illegal values. The status description contains additional information about the error.
404	Indicates the requested execution was not found or the execution does not have a variable with the given name in the requested scope (in case scope-query parameter was omitted, variable doesn't exist in local and global scope). Status description contains additional information about the error.

Success response body:

   {
      "name":"intProcVar",
      "type":"integer",
      "value":123,
      "scope":"local"
   }
In case the variable is a binary variable or serializable, the valueUrl points to an URL to fetch the raw value. If it's a plain variable, the value is present in the response.


EOF
        ],
        [
            <<<EOF
            Create (or update) variables on an execution
EOF
            , 'POST runtime/executions/{executionId}/variables', 'POST', 'runtime/executions/{executionId}/variables',
            <<<EOF

PUT runtime/executions/{executionId}/variables
When using POST, all variables that are passed are created. In case one of the variables already exists on the execution in the requested scope, the request results in an error (409 - CONFLICT). When PUT is used, unexisting variables are created on the execution and existing ones are overridden without any error.

Table 15.101. Create (or update) variables on an execution - URL parameters
Parameter	Required	Value	Description
executionId	Yes	String	The id of the execution to the variables for.

Request body:

[
   {
      "name":"intProcVar"
      "type":"integer"
      "value":123,
      "scope":"local"
   },

   ...
]
Note that you can only provide variables that have the same scope. If the request-body array contains variables from mixed scopes, the request results in an error (400 - BAD REQUEST).Any number of variables can be passed into the request body array. More information about the variable format can be found in the REST variables section. Note that scope is ignored, only local variables can be set in a process instance.

Table 15.102. Create (or update) variables on an execution - Response codes
Response code	Description
201	Indicates the execution was found and variable is created.
400	Indicates the request body is incomplete or contains illegal values. The status description contains additional information about the error.
404	Indicates the requested execution was not found.
409	Indicates the execution was found but already contains a variable with the given name (only thrown when POST method is used). Use the update-method instead.

Success response body:

[
   {
      "name":"intProcVar",
      "type":"integer",
      "value":123,
      "scope":"local"
   },

   ...

]

EOF
        ],
        [
            <<<EOF
            Update a variable on an execution
EOF
            , 'PUT runtime/executions/{executionId}/variables/{variableName}', 'PUT', 'runtime/executions/{executionId}/variables/{variableName}',
            <<<EOF

Table 15.103. Update a variable on an execution - URL parameters
Parameter	Required	Value	Description
executionId	Yes	String	The id of the execution to update the variables for.
variableName	Yes	String	Name of the variable to update.

Request body:

 {
    "name":"intProcVar"
    "type":"integer"
    "value":123,
    "scope":"global"
 }
More information about the variable format can be found in the REST variables section.

Table 15.104. Update a variable on an execution - Response codes
Response code	Description
200	Indicates both the process instance and variable were found and variable is updated.
404	Indicates the requested process instance was not found or the process instance does not have a variable with the given name. Status description contains additional information about the error.

Success response body:

   {
      "name":"intProcVar",
      "type":"integer",
      "value":123,
      "scope":"global"
   }
In case the variable is a binary variable or serializable, the valueUrl points to an URL to fetch the raw value. If it's a plain variable, the value is present in the response.


EOF
        ],
        [
            <<<EOF
            Create a new binary variable on an execution
EOF
            , 'POST runtime/executions/{executionId}/variables', 'POST', 'runtime/executions/{executionId}/variables',
            <<<EOF

Table 15.105. Create a new binary variable on an execution - URL parameters
Parameter	Required	Value	Description
executionId	Yes	String	The id of the execution to create the new variable for.

Request body: The request should be of type multipart/form-data. There should be a single file-part included with the binary value of the variable. On top of that, the following additional form-fields can be present:

name: Required name of the variable.
type: Type of variable that is created. If omitted, binary is assumed and the binary data in the request will be stored as an array of bytes.
scope: Scope of variable that is created. If omitted, local is assumed.
Success response body:

{
  "name" : "binaryVariable",
  "scope" : "local",
  "type" : "binary",
  "value" : null,
  "valueUrl" : "http://.../runtime/executions/123/variables/binaryVariable/data"
}
Table 15.106. Create a new binary variable on an execution - Response codes
Response code	Description
201	Indicates the variable was created and the result is returned.
400	Indicates the name of the variable to create was missing. Status message provides additional information.
404	Indicates the requested execution was not found.
409	Indicates the execution already has a variable with the given name. Use the PUT method to update the task variable instead.
415	Indicates the serializable data contains an object for which no class is present in the JVM running the Activiti engine and therefore cannot be deserialized.


EOF
        ],
        [
            <<<EOF
            Update an existing binary variable on a process-instance
EOF
            , 'PUT runtime/executions/{executionId}/variables/{variableName}', 'PUT', 'runtime/executions/{executionId}/variables/{variableName}',
            <<<EOF

Table 15.107. Update an existing binary variable on a process-instance - URL parameters
Parameter	Required	Value	Description
executionId	Yes	String	The id of the execution to create the new variable for.
variableName	Yes	String	The name of the variable to update.

Request body: The request should be of type multipart/form-data. There should be a single file-part included with the binary value of the variable. On top of that, the following additional form-fields can be present:

name: Required name of the variable.
type: Type of variable that is created. If omitted, binary is assumed and the binary data in the request will be stored as an array of bytes.
scope: Scope of variable that is created. If omitted, local is assumed.
Success response body:

{
  "name" : "binaryVariable",
  "scope" : "local",
  "type" : "binary",
  "value" : null,
  "valueUrl" : "http://.../runtime/executions/123/variables/binaryVariable/data"
}
Table 15.108. Update an existing binary variable on a process-instance - Response codes
Response code	Description
200	Indicates the variable was updated and the result is returned.
400	Indicates the name of the variable to update was missing. Status message provides additional information.
404	Indicates the requested execution was not found or the execution does not have a variable with the given name.
415	Indicates the serializable data contains an object for which no class is present in the JVM running the Activiti engine and therefore cannot be deserialized.

Tasks

EOF
        ],
        [
            <<<EOF
            Get a task
EOF
            , 'GET runtime/tasks/{taskId}', 'GET', 'runtime/tasks/{taskId}',
            <<<EOF

Table 15.109. Get a task - URL parameters
Parameter	Required	Value	Description
taskId	Yes	String	The id of the task to get.

Table 15.110. Get a task - Response codes
Response code	Description
200	Indicates the task was found and returned.
404	Indicates the requested task was not found.

Success response body:

{
  "assignee" : "kermit",
  "createTime" : "2013-04-17T10:17:43.902+0000",
  "delegationState" : "pending",
  "description" : "Task description",
  "dueDate" : "2013-04-17T10:17:43.902+0000",
  "execution" : "http://localhost:8182/runtime/executions/5",
  "id" : "8",
  "name" : "My task",
  "owner" : "owner",
  "parentTask" : "http://localhost:8182/runtime/tasks/9",
  "priority" : 50,
  "processDefinition" : "http://localhost:8182/repository/process-definitions/oneTaskProcess%3A1%3A4",
  "processInstance" : "http://localhost:8182/runtime/process-instances/5",
  "suspended" : false,
  "taskDefinitionKey" : "theTask",
  "url" : "http://localhost:8182/runtime/tasks/8",
  "tenantId" : null
}
delegationState: Delegation-state of the task, can be null, "pending" or "resolved".

EOF
        ],
        [
            <<<EOF
            List of tasks
EOF
            , 'GET runtime/tasks', 'GET', 'runtime/tasks',
            <<<EOF

Table 15.111. List of tasks - URL query parameters
Parameter	Required	Value	Description
name	No	String	Only return tasks with the given name.
nameLike	No	String	Only return tasks with a name like the given name.
description	No	String	Only return tasks with the given description.
priority	No	Integer	Only return tasks with the given priotiry.
minimumPriority	No	Integer	Only return tasks with a priority greater than the given value.
maximumPriority	No	Integer	Only return tasks with a priority lower than the given value.
assignee	No	String	Only return tasks assigned to the given user.
assigneeLike	No	String	Only return tasks assigned with an assignee like the given value.
owner	No	String	Only return tasks owned by the given user.
ownerLike	No	String	Only return tasks assigned with an owner like the given value.
unassigned	No	Boolean	Only return tasks that are not assigned to anyone. If false is passed, the value is ignored.
delegationState	No	String	Only return tasks that have the given delegation state. Possible values are pending and resolved.
candidateUser	No	String	Only return tasks that can be claimed by the given user. This includes both tasks where the user is an explicit candidate for and task that are claimable by a group that the user is a member of.
candidateGroup	No	String	Only return tasks that can be claimed by a user in the given group.
candidateGroups	No	String	Only return tasks that can be claimed by a user in the given groups. Values split by comma.
involvedUser	No	String	Only return tasks in which the given user is involved.
taskDefinitionKey	No	String	Only return tasks with the given task definition id.
taskDefinitionKeyLike	No	String	Only return tasks with a given task definition id like the given value.
processInstanceId	No	String	Only return tasks which are part of the process instance with the given id.
processInstanceBusinessKey	No	String	Only return tasks which are part of the process instance with the given business key.
processInstanceBusinessKeyLike	No	String	Only return tasks which are part of the process instance which has a business key like the given value.
processDefinitionKey	No	String	Only return tasks which are part of a process instance which has a process definition with the given key.
processDefinitionKeyLike	No	String	Only return tasks which are part of a process instance which has a process definition with a key like the given value.
processDefinitionName	No	String	Only return tasks which are part of a process instance which has a process definition with the given name.
processDefinitionNameLike	No	String	Only return tasks which are part of a process instance which has a process definition with a name like the given value.
executionId	No	String	Only return tasks which are part of the execution with the given id.
createdOn	No	ISO Date	Only return tasks which are created on the given date.
createdBefore	No	ISO Date	Only return tasks which are created before the given date.
createdAfter	No	ISO Date	Only return tasks which are created after the given date.
dueOn	No	ISO Date	Only return tasks which are due on the given date.
dueBefore	No	ISO Date	Only return tasks which are due before the given date.
dueAfter	No	ISO Date	Only return tasks which are due after the given date.
withoutDueDate	No	boolean	Only return tasks which don't have a due date. The property is ignored if the value is false.
withoutDueDate	No	boolean	Only return tasks which don't have a due date. The property is ignored if the value is false.
withoutDueDate	No	boolean	Only return tasks which don't have a due date. The property is ignored if the value is false.
excludeSubTasks	No	Boolean	Only return tasks that are not a subtask of another task.
active	No	Boolean	If true, only return tasks that are not suspended (either part of a process that is not suspended or not part of a process at all). If false, only tasks that are part of suspended process instances are returned.
includeTaskLocalVariables	No	Boolean	Indication to include task local variables in the result.
includeProcessVariables	No	Boolean	Indication to include process variables in the result.
tenantId	No	String	Only return tasks with the given tenantId.
tenantIdLike	No	String	Only return tasks with a tenantId like the given value.
withoutTenantId	No	Boolean	If true, only returns tasks without a tenantId set. If false, the withoutTenantId parameter is ignored.
candidateOrAssigned	No	String	Select tasks that has been claimed or assigned to user or waiting to claim by user (candidate user or groups).
The general paging and sorting query-parameters can be used for this URL.



Table 15.112. List of tasks - Response codes
Response code	Description
200	Indicates request was successful and the tasks are returned
400	Indicates a parameter was passed in the wrong format or that 'delegationState' has an invalid value (other than 'pending' and 'resolved'). The status-message contains additional information.

Success response body:

{
  "data": [
    {
      "assignee" : "kermit",
      "createTime" : "2013-04-17T10:17:43.902+0000",
      "delegationState" : "pending",
      "description" : "Task description",
      "dueDate" : "2013-04-17T10:17:43.902+0000",
      "execution" : "http://localhost:8182/runtime/executions/5",
      "id" : "8",
      "name" : "My task",
      "owner" : "owner",
      "parentTask" : "http://localhost:8182/runtime/tasks/9",
      "priority" : 50,
      "processDefinition" : "http://localhost:8182/repository/process-definitions/oneTaskProcess%3A1%3A4",
      "processInstance" : "http://localhost:8182/runtime/process-instances/5",
      "suspended" : false,
      "taskDefinitionKey" : "theTask",
      "url" : "http://localhost:8182/runtime/tasks/8",
      "tenantId" : null
    }
  ],
  "total": 1,
  "start": 0,
  "sort": "name",
  "order": "asc",
  "size": 1
}

EOF
        ],
        [
            <<<EOF
            Query for tasks
EOF
            , 'POST query/tasks', 'POST', 'query/tasks',
            <<<EOF

Request body:

{
  "name" : "My task",
  "description" : "The task description",

  ...

  "taskVariables" : [
    {
      "name" : "myVariable",
      "value" : 1234,
      "operation" : "equals",
      "type" : "long"
    }
  ],

    "processInstanceVariables" : [
      {
         ...
      }
    ]
  ]
}
All supported JSON parameter fields allowed are exactly the same as the parameters found for getting a collection of tasks (except for candidateGroupIn which is only available in this POST task query REST service), but passed in as JSON-body arguments rather than URL-parameters to allow for more advanced querying and preventing errors with request-uri's that are too long. On top of that, the query allows for filtering based on task and process variables. The taskVariables and processInstanceVariables are both json-arrays containing objects with the format as described here.

Table 15.113. Query for tasks - Response codes
Response code	Description
200	Indicates request was successful and the tasks are returned
400	Indicates a parameter was passed in the wrong format or that 'delegationState' has an invalid value (other than 'pending' and 'resolved'). The status-message contains additional information.

Success response body:

{
  "data": [
    {
      "assignee" : "kermit",
      "createTime" : "2013-04-17T10:17:43.902+0000",
      "delegationState" : "pending",
      "description" : "Task description",
      "dueDate" : "2013-04-17T10:17:43.902+0000",
      "execution" : "http://localhost:8182/runtime/executions/5",
      "id" : "8",
      "name" : "My task",
      "owner" : "owner",
      "parentTask" : "http://localhost:8182/runtime/tasks/9",
      "priority" : 50,
      "processDefinition" : "http://localhost:8182/repository/process-definitions/oneTaskProcess%3A1%3A4",
      "processInstance" : "http://localhost:8182/runtime/process-instances/5",
      "suspended" : false,
      "taskDefinitionKey" : "theTask",
      "url" : "http://localhost:8182/runtime/tasks/8",
      "tenantId" : null
    }
  ],
  "total": 1,
  "start": 0,
  "sort": "name",
  "order": "asc",
  "size": 1
}

EOF
        ],
        [
            <<<EOF
            Update a task
EOF
            , 'PUT runtime/tasks/{taskId}', 'PUT', 'runtime/tasks/{taskId}',
            <<<EOF

Body JSON:

{
  "assignee" : "assignee",
  "delegationState" : "resolved",
  "description" : "New task description",
  "dueDate" : "2013-04-17T13:06:02.438+02:00",
  "name" : "New task name",
  "owner" : "owner",
  "parentTaskId" : "3",
  "priority" : 20
}
All request values are optional. For example, you can only include the 'assignee' attribute in the request body JSON-object, only updating the assignee of the task, leaving all other fields unaffected. When an attribute is explicitly included and is set to null, the task-value will be updated to null. Example: {"dueDate" : null} will clear the duedate of the task).

Table 15.114. Update a task - Response codes
Response code	Description
200	Indicates the task was updated.
404	Indicates the requested task was not found.
409	Indicates the requested task was updated simultaneously.

Success response body: see response for runtime/tasks/{taskId}.


EOF
        ],
        [
            <<<EOF
            Task actions
EOF
            , 'POST runtime/tasks/{taskId}', 'POST', 'runtime/tasks/{taskId}',
            <<<EOF

Complete a task - Body JSON:

{
  "action" : "complete",
  "variables" : ...
}
Completes the task. Optional variable array can be passed in using the variables property. More information about the variable format can be found in the REST variables section. Note that the variable-scope that is supplied is ignored and the variables are set on the parent-scope unless a variable exists in a local scope, which is overridden in this case. This is the same behavior as the TaskService.completeTask(taskId, variables) invocation.

Claim a task - Body JSON:

{
  "action" : "claim",
  "assignee" : "userWhoClaims"
}
Claims the task by the given assignee. If the assignee is null, the task is assigned to no-one, claimable agian.

Delegate a task - Body JSON:

{
  "action" : "delegate",
  "assignee" : "userToDelegateTo"
}
Delegates the task to the given assignee. The assignee is required.

Resolve a task - Body JSON:

{
  "action" : "resolve"
}
Resolves the task delegation. The task is assigned back to the task owner (if any).

Table 15.115. Task actions - Response codes
Response code	Description
200	Indicates the action was executed.
400	When the body contains an invalid value or when the assignee is missing when the action requires it.
404	Indicates the requested task was not found.
409	Indicates the action cannot be performed due to a conflict. Either the task was updates simultaneously or the task was claimed by another user, in case of the 'claim' action.

Success response body: see response for runtime/tasks/{taskId}.


EOF
        ],
        [
            <<<EOF
            Delete a task
EOF
            , 'DELETE runtime/tasks/{taskId}?cascadeHistory={cascadeHistory}&deleteReason={deleteReason}', 'DELETE', 'runtime/tasks/{taskId}?cascadeHistory={cascadeHistory}&deleteReason={deleteReason}',
            <<<EOF

Table 15.116. >Delete a task - URL parameters
Parameter	Required	Value	Description
taskId	Yes	String	The id of the task to delete.
cascadeHistory	False	Boolean	Whether or not to delete the HistoricTask instance when deleting the task (if applicable). If not provided, this value defaults to false.
deleteReason	False	String	Reason why the task is deleted. This value is ignored when cascadeHistory is true.

Table 15.117. >Delete a task - Response codes
Response code	Description
204	Indicates the task was found and has been deleted. Response-body is intentionally empty.
403	Indicates the requested task cannot be deleted because it's part of a workflow.
404	Indicates the requested task was not found.


EOF
        ],
        [
            <<<EOF
            Get all variables for a task
EOF
            , 'GET runtime/tasks/{taskId}/variables?scope={scope}', 'GET', 'runtime/tasks/{taskId}/variables?scope={scope}',
            <<<EOF

Table 15.118. Get all variables for a task - URL parameters
Parameter	Required	Value	Description
taskId	Yes	String	The id of the task to get variables for.
scope	False	String	Scope of variables to be returned. When 'local', only task-local variables are returned. When 'global', only variables from the task's parent execution-hierarchy are returned. When the parameter is omitted, both local and global variables are returned.

Table 15.119. Get all variables for a task - Response codes
Response code	Description
200	Indicates the task was found and the requested variables are returned.
404	Indicates the requested task was not found.

Success response body:

[
  {
    "name" : "doubleTaskVar",
    "scope" : "local",
    "type" : "double",
    "value" : 99.99
  },
  {
    "name" : "stringProcVar",
    "scope" : "global",
    "type" : "string",
    "value" : "This is a ProcVariable"
  },

  ...

]
The variables are returned as a JSON array. Full response description can be found in the general REST-variables section.


EOF
        ],
        [
            <<<EOF
            Get a variable from a task
EOF
            , 'GET runtime/tasks/{taskId}/variables/{variableName}?scope={scope}', 'GET', 'runtime/tasks/{taskId}/variables/{variableName}?scope={scope}',
            <<<EOF

Table 15.120. Get a variable from a task - URL parameters
Parameter	Required	Value	Description
taskId	Yes	String	The id of the task to get a variable for.
variableName	Yes	String	The name of the variable to get.
scope	False	String	Scope of variable to be returned. When 'local', only task-local variable value is returned. When 'global', only variable value from the task's parent execution-hierarchy are returned. When the parameter is omitted, a local variable will be returned if it exists, otherwise a global variable.

Table 15.121. Get a variable from a task - Response codes
Response code	Description
200	Indicates the task was found and the requested variables are returned.
404	Indicates the requested task was not found or the task doesn't have a variable with the given name (in the given scope). Status message provides additional information.

Success response body:

{
  "name" : "myTaskVariable",
  "scope" : "local",
  "type" : "string",
  "value" : "Hello my friend"
}
Full response body description can be found in the general REST-variables section.


EOF
        ],
        [
            <<<EOF
            Get the binary data for a variable
EOF
            , 'GET runtime/tasks/{taskId}/variables/{variableName}/data?scope={scope}', 'GET', 'runtime/tasks/{taskId}/variables/{variableName}/data?scope={scope}',
            <<<EOF

Table 15.122. Get the binary data for a variable - URL parameters
Parameter	Required	Value	Description
taskId	Yes	String	The id of the task to get a variable data for.
variableName	Yes	String	The name of the variable to get data for. Only variables of type binary and serializable can be used. If any other type of variable is used, a 404 is returned.
scope	False	String	Scope of variable to be returned. When 'local', only task-local variable value is returned. When 'global', only variable value from the task's parent execution-hierarchy are returned. When the parameter is omitted, a local variable will be returned if it exists, otherwise a global variable.

Table 15.123. Get the binary data for a variable - Response codes
Response code	Description
200	Indicates the task was found and the requested variables are returned.
404	Indicates the requested task was not found or the task doesn't have a variable with the given name (in the given scope) or the variable doesn't have a binary stream available. Status message provides additional information.

Success response body: The response body contains the binary value of the variable. When the variable is of type binary, the content-type of the response is set to application/octet-stream, regardless of the content of the variable or the request accept-type header. In case of serializable, application/x-java-serialized-object is used as content-type.


EOF
        ],
        [
            <<<EOF
            Create new variables on a task
EOF
            , 'POST runtime/tasks/{taskId}/variables', 'POST', 'runtime/tasks/{taskId}/variables',
            <<<EOF

Table 15.124. Create new variables on a task - URL parameters
Parameter	Required	Value	Description
taskId	Yes	String	The id of the task to create the new variable for.

Request body for creating simple (non-binary) variables:

[
  {
    "name" : "myTaskVariable",
    "scope" : "local",
    "type" : "string",
    "value" : "Hello my friend"
  },
  {
    ...
  }
]
The request body should be an array containing one or more JSON-objects representing the variables that should be created.

name: Required name of the variable
scope: Scope of variable that is created. If omitted, local is assumed.
type: Type of variable that is created. If omitted, reverts to raw JSON-value type (string, boolean, integer or double).
value: Variable value.
More information about the variable format can be found in the REST variables section.

Success response body:

[
  {
    "name" : "myTaskVariable",
    "scope" : "local",
    "type" : "string",
    "value" : "Hello my friend"
  },
  {
    ...
  }
]
Table 15.125. Create new variables on a task - Response codes
Response code	Description
201	Indicates the variables were created and the result is returned.
400	Indicates the name of a variable to create was missing or that an attempt is done to create a variable on a standalone task (without a process associated) with scope global or an empty array of variables was included in the request or request did not contain an array of variables. Status message provides additional information.
404	Indicates the requested task was not found.
409	Indicates the task already has a variable with the given name. Use the PUT method to update the task variable instead.


EOF
        ],
        [
            <<<EOF
            Create a new binary variable on a task
EOF
            , 'POST runtime/tasks/{taskId}/variables', 'POST', 'runtime/tasks/{taskId}/variables',
            <<<EOF

Table 15.126. Create a new binary variable on a task - URL parameters
Parameter	Required	Value	Description
taskId	Yes	String	The id of the task to create the new variable for.

Request body: The request should be of type multipart/form-data. There should be a single file-part included with the binary value of the variable. On top of that, the following additional form-fields can be present:

name: Required name of the variable.
scope: Scope of variable that is created. If omitted, local is assumed.
type: Type of variable that is created. If omitted, binary is assumed and the binary data in the request will be stored as an array of bytes.
Success response body:

{
  "name" : "binaryVariable",
  "scope" : "local",
  "type" : "binary",
  "value" : null,
  "valueUrl" : "http://.../runtime/tasks/123/variables/binaryVariable/data"
}
Table 15.127. Create a new binary variable on a task - Response codes
Response code	Description
201	Indicates the variable was created and the result is returned.
400	Indicates the name of the variable to create was missing or that an attempt is done to create a variable on a standalone task (without a process associated) with scope global. Status message provides additional information.
404	Indicates the requested task was not found.
409	Indicates the task already has a variable with the given name. Use the PUT method to update the task variable instead.
415	Indicates the serializable data contains an object for which no class is present in the JVM running the Activiti engine and therefore cannot be deserialized.


EOF
        ],
        [
            <<<EOF
            Update an existing variable on a task
EOF
            , 'PUT runtime/tasks/{taskId}/variables/{variableName}', 'PUT', 'runtime/tasks/{taskId}/variables/{variableName}',
            <<<EOF

Table 15.128. Update an existing variable on a task - URL parameters
Parameter	Required	Value	Description
taskId	Yes	String	The id of the task to update the variable for.
variableName	Yes	String	The name of the variable to update.

Request body for updating simple (non-binary) variables:

{
  "name" : "myTaskVariable",
  "scope" : "local",
  "type" : "string",
  "value" : "Hello my friend"
}
name: Required name of the variable
scope: Scope of variable that is updated. If omitted, local is assumed.
type: Type of variable that is updated. If omitted, reverts to raw JSON-value type (string, boolean, integer or double).
value: Variable value.
More information about the variable format can be found in the REST variables section.

Success response body:

{
  "name" : "myTaskVariable",
  "scope" : "local",
  "type" : "string",
  "value" : "Hello my friend"
}
Table 15.129. Update an existing variable on a task - Response codes
Response code	Description
200	Indicates the variables was updated and the result is returned.
400	Indicates the name of a variable to update was missing or that an attempt is done to update a variable on a standalone task (without a process associated) with scope global. Status message provides additional information.
404	Indicates the requested task was not found or the task doesn't have a variable with the given name in the given scope. Status message contains additional information about the error.


EOF
        ],
        [
            <<<EOF
            Updating a binary variable on a task
EOF
            , 'PUT runtime/tasks/{taskId}/variables/{variableName}', 'PUT', 'runtime/tasks/{taskId}/variables/{variableName}',
            <<<EOF

Table 15.130. Updating a binary variable on a task - URL parameters
Parameter	Required	Value	Description
taskId	Yes	String	The id of the task to update the variable for.
variableName	Yes	String	The name of the variable to update.

Request body: The request should be of type multipart/form-data. There should be a single file-part included with the binary value of the variable. On top of that, the following additional form-fields can be present:

name: Required name of the variable.
scope: Scope of variable that is updated. If omitted, local is assumed.
type: Type of variable that is updated. If omitted, binary is assumed and the binary data in the request will be stored as an array of bytes.
Success response body:

{
  "name" : "binaryVariable",
  "scope" : "local",
  "type" : "binary",
  "value" : null,
  "valueUrl" : "http://.../runtime/tasks/123/variables/binaryVariable/data"
}
Table 15.131. Updating a binary variable on a task - Response codes
Response code	Description
200	Indicates the variable was updated and the result is returned.
400	Indicates the name of the variable to update was missing or that an attempt is done to update a variable on a standalone task (without a process associated) with scope global. Status message provides additional information.
404	Indicates the requested task was not found or the variable to update doesn't exist for the given task in the given scope.
415	Indicates the serializable data contains an object for which no class is present in the JVM running the Activiti engine and therefore cannot be deserialized.


EOF
        ],
        [
            <<<EOF
            Delete a variable on a task
EOF
            , 'DELETE runtime/tasks/{taskId}/variables/{variableName}?scope={scope}', 'DELETE', 'runtime/tasks/{taskId}/variables/{variableName}?scope={scope}',
            <<<EOF

Table 15.132. Delete a variable on a task - URL parameters
Parameter	Required	Value	Description
taskId	Yes	String	The id of the task the variable to delete belongs to.
variableName	Yes	String	The name of the variable to delete.
scope	No	String	Scope of variable to delete in. Can be either local or global. If omitted, local is assumed.

Table 15.133. Delete a variable on a task - Response codes
Response code	Description
204	Indicates the task variable was found and has been deleted. Response-body is intentionally empty.
404	Indicates the requested task was not found or the task doesn't have a variable with the given name. Status message contains additional information about the error.


EOF
        ],
        [
            <<<EOF
            Delete all local variables on a task
EOF
            , 'DELETE runtime/tasks/{taskId}/variables', 'DELETE', 'runtime/tasks/{taskId}/variables',
            <<<EOF

Table 15.134. Delete all local variables on a task - URL parameters
Parameter	Required	Value	Description
taskId	Yes	String	The id of the task the variable to delete belongs to.

Table 15.135. Delete all local variables on a task - Response codes
Response code	Description
204	Indicates all local task variables have been deleted. Response-body is intentionally empty.
404	Indicates the requested task was not found.


EOF
        ],
        [
            <<<EOF
            Get all identity links for a task
EOF
            , 'GET runtime/tasks/{taskId}/identitylinks', 'GET', 'runtime/tasks/{taskId}/identitylinks',
            <<<EOF

Table 15.136. Get all identity links for a task - URL parameters
Parameter	Required	Value	Description
taskId	Yes	String	The id of the task to get the identity links for.

Table 15.137. Get all identity links for a task - Response codes
Response code	Description
200	Indicates the task was found and the requested identity links are returned.
404	Indicates the requested task was not found.

Success response body:

[
  {
    "userId" : "kermit",
    "groupId" : null,
    "type" : "candidate",
    "url" : "http://localhost:8081/activiti-rest/service/runtime/tasks/100/identitylinks/users/kermit/candidate"
  },
  {
    "userId" : null,
    "groupId" : "sales",
    "type" : "candidate",
    "url" : "http://localhost:8081/activiti-rest/service/runtime/tasks/100/identitylinks/groups/sales/candidate"
  },

  ...
]

EOF
        ],
        [
            <<<EOF
            Get all identitylinks for a task for either groups or users
EOF
            , 'GET runtime/tasks/{taskId}/identitylinks/users', 'GET', 'runtime/tasks/{taskId}/identitylinks/users',
            <<<EOF

GET runtime/tasks/{taskId}/identitylinks/groups
Returns only identity links targetting either users or groups. Response body and status-codes are exactly the same as when getting the full list of identity links for a task.


EOF
        ],
        [
            <<<EOF
            Get a single identity link on a task
EOF
            , 'GET runtime/tasks/{taskId}/identitylinks/{family}/{identityId}/{type}', 'GET', 'runtime/tasks/{taskId}/identitylinks/{family}/{identityId}/{type}',
            <<<EOF

Table 15.138. Get all identitylinks for a task for either groups or users - URL parameters
Parameter	Required	Value	Description
taskId	Yes	String	The id of the task .
family	Yes	String	Either groups or users, depending on what kind of identity is targetted.
identityId	Yes	String	The id of the identity.
type	Yes	String	The type of identity link.

Table 15.139. Get all identitylinks for a task for either groups or users - Response codes
Response code	Description
200	Indicates the task and identity link was found and returned.
404	Indicates the requested task was not found or the task doesn't have the requested identityLink. The status contains additional information about this error.

Success response body:

{
  "userId" : null,
  "groupId" : "sales",
  "type" : "candidate",
  "url" : "http://localhost:8081/activiti-rest/service/runtime/tasks/100/identitylinks/groups/sales/candidate"
}

EOF
        ],
        [
            <<<EOF
            Create an identity link on a task
EOF
            , 'POST runtime/tasks/{taskId}/identitylinks', 'POST', 'runtime/tasks/{taskId}/identitylinks',
            <<<EOF

Table 15.140. Create an identity link on a task - URL parameters
Parameter	Required	Value	Description
taskId	Yes	String	The id of the task .

Request body (user):

{
  "userId" : "kermit",
  "type" : "candidate",
}
Request body (group):

{
  "groupId" : "sales",
  "type" : "candidate",
}
Table 15.141. Create an identity link on a task - Response codes
Response code	Description
201	Indicates the task was found and the identity link was created.
404	Indicates the requested task was not found or the task doesn't have the requested identityLink. The status contains additional information about this error.

Success response body:

{
  "userId" : null,
  "groupId" : "sales",
  "type" : "candidate",
  "url" : "http://localhost:8081/activiti-rest/service/runtime/tasks/100/identitylinks/groups/sales/candidate"
}

EOF
        ],
        [
            <<<EOF
            Delete an identity link on a task
EOF
            , 'DELETE runtime/tasks/{taskId}/identitylinks/{family}/{identityId}/{type}', 'DELETE', 'runtime/tasks/{taskId}/identitylinks/{family}/{identityId}/{type}',
            <<<EOF

Table 15.142. Delete an identity link on a task - URL parameters
Parameter	Required	Value	Description
taskId	Yes	String	The id of the task.
family	Yes	String	Either groups or users, depending on what kind of identity is targetted.
identityId	Yes	String	The id of the identity.
type	Yes	String	The type of identity link.

Table 15.143. Delete an identity link on a task - Response codes
Response code	Description
204	Indicates the task and identity link were found and the link has been deleted. Response-body is intentionally empty.
404	Indicates the requested task was not found or the task doesn't have the requested identityLink. The status contains additional information about this error.


EOF
        ],
        [
            <<<EOF
            Create a new comment on a task
EOF
            , 'POST runtime/tasks/{taskId}/comments', 'POST', 'runtime/tasks/{taskId}/comments',
            <<<EOF

Table 15.144. Create a new comment on a task - URL parameters
Parameter	Required	Value	Description
taskId	Yes	String	The id of the task to create the comment for.

Request body:

{
  "message" : "This is a comment on the task.",
  "saveProcessInstanceId" : true
}
Parameter saveProcessInstanceId is optional, if true save process instance id of task with comment.

Success response body:

{
  "id" : "123",
  "taskUrl" : "http://localhost:8081/activiti-rest/service/runtime/tasks/101/comments/123",
  "processInstanceUrl" : "http://localhost:8081/activiti-rest/service/history/historic-process-instances/100/comments/123",
  "message" : "This is a comment on the task.",
  "author" : "kermit",
  "time" : "2014-07-13T13:13:52.232+08:00"
  "taskId" : "101",
  "processInstanceId" : "100"
}
Table 15.145. Create a new comment on a task - Response codes
Response code	Description
201	Indicates the comment was created and the result is returned.
400	Indicates the comment is missing from the request.
404	Indicates the requested task was not found.


EOF
        ],
        [
            <<<EOF
            Get all comments on a task
EOF
            , 'GET runtime/tasks/{taskId}/comments', 'GET', 'runtime/tasks/{taskId}/comments',
            <<<EOF

Table 15.146. Get all comments on a task - URL parameters
Parameter	Required	Value	Description
taskId	Yes	String	The id of the task to get the comments for.

Success response body:

[
  {
    "id" : "123",
    "taskUrl" : "http://localhost:8081/activiti-rest/service/runtime/tasks/101/comments/123",
    "processInstanceUrl" : "http://localhost:8081/activiti-rest/service/history/historic-process-instances/100/comments/123",
    "message" : "This is a comment on the task.",
    "author" : "kermit"
    "time" : "2014-07-13T13:13:52.232+08:00"
    "taskId" : "101",
    "processInstanceId" : "100"
  },
  {
    "id" : "456",
    "taskUrl" : "http://localhost:8081/activiti-rest/service/runtime/tasks/101/comments/456",
    "processInstanceUrl" : "http://localhost:8081/activiti-rest/service/history/historic-process-instances/100/comments/456",
    "message" : "This is another comment on the task.",
    "author" : "gonzo",
    "time" : "2014-07-13T13:13:52.232+08:00"
    "taskId" : "101",
    "processInstanceId" : "100"
  }
]
Table 15.147. Get all comments on a task - Response codes
Response code	Description
200	Indicates the task was found and the comments are returned.
404	Indicates the requested task was not found.


EOF
        ],
        [
            <<<EOF
            Get a comment on a task
EOF
            , 'GET runtime/tasks/{taskId}/comments/{commentId}', 'GET', 'runtime/tasks/{taskId}/comments/{commentId}',
            <<<EOF

Table 15.148. Get a comment on a task - URL parameters
Parameter	Required	Value	Description
taskId	Yes	String	The id of the task to get the comment for.
commentId	Yes	String	The id of the comment.

Success response body:

{
  "id" : "123",
  "taskUrl" : "http://localhost:8081/activiti-rest/service/runtime/tasks/101/comments/123",
  "processInstanceUrl" : "http://localhost:8081/activiti-rest/service/history/historic-process-instances/100/comments/123",
  "message" : "This is a comment on the task.",
  "author" : "kermit",
  "time" : "2014-07-13T13:13:52.232+08:00"
  "taskId" : "101",
  "processInstanceId" : "100"
}
Table 15.149. Get a comment on a task - Response codes
Response code	Description
200	Indicates the task and comment were found and the comment is returned.
404	Indicates the requested task was not found or the tasks doesn't have a comment with the given ID.


EOF
        ],
        [
            <<<EOF
            Delete a comment on a task
EOF
            , 'DELETE runtime/tasks/{taskId}/comments/{commentId}', 'DELETE', 'runtime/tasks/{taskId}/comments/{commentId}',
            <<<EOF

Table 15.150. Delete a comment on a task - URL parameters
Parameter	Required	Value	Description
taskId	Yes	String	The id of the task to delete the comment for.
commentId	Yes	String	The id of the comment.

Table 15.151. Delete a comment on a task - Response codes
Response code	Description
204	Indicates the task and comment were found and the comment is deleted. Response body is left empty intentionally.
404	Indicates the requested task was not found or the tasks doesn't have a comment with the given ID.


EOF
        ],
        [
            <<<EOF
            Get all events for a task
EOF
            , 'GET runtime/tasks/{taskId}/events', 'GET', 'runtime/tasks/{taskId}/events',
            <<<EOF

Table 15.152. Get all events for a task - URL parameters
Parameter	Required	Value	Description
taskId	Yes	String	The id of the task to get the events for.

Success response body:

[
  {
    "action" : "AddUserLink",
    "id" : "4",
    "message" : [ "gonzo", "contributor" ],
    "taskUrl" : "http://localhost:8182/runtime/tasks/2",
    "time" : "2013-05-17T11:50:50.000+0000",
    "url" : "http://localhost:8182/runtime/tasks/2/events/4",
    "userId" : null
  },

  ...

]
Table 15.153. Get all events for a task - Response codes
Response code	Description
200	Indicates the task was found and the events are returned.
404	Indicates the requested task was not found.


EOF
        ],
        [
            <<<EOF
            Get an event on a task
EOF
            , 'GET runtime/tasks/{taskId}/events/{eventId}', 'GET', 'runtime/tasks/{taskId}/events/{eventId}',
            <<<EOF

Table 15.154. Get an event on a task - URL parameters
Parameter	Required	Value	Description
taskId	Yes	String	The id of the task to get the event for.
eventId	Yes	String	The id of the event.

Success response body:

{
  "action" : "AddUserLink",
  "id" : "4",
  "message" : [ "gonzo", "contributor" ],
  "taskUrl" : "http://localhost:8182/runtime/tasks/2",
  "time" : "2013-05-17T11:50:50.000+0000",
  "url" : "http://localhost:8182/runtime/tasks/2/events/4",
  "userId" : null
}
Table 15.155. Get an event on a task - Response codes
Response code	Description
200	Indicates the task and event were found and the event is returned.
404	Indicates the requested task was not found or the tasks doesn't have an event with the given ID.


EOF
        ],
        [
            <<<EOF
            Create a new attachment on a task, containing a link to an external resource
EOF
            , 'POST runtime/tasks/{taskId}/attachments', 'POST', 'runtime/tasks/{taskId}/attachments',
            <<<EOF

Table 15.156. Create a new attachment on a task, containing a link to an external resource - URL parameters
Parameter	Required	Value	Description
taskId	Yes	String	The id of the task to create the attachment for.

Request body:

{
  "name":"Simple attachment",
  "description":"Simple attachment description",
  "type":"simpleType",
  "externalUrl":"http://activiti.org"
}
Only the attachment name is required to create a new attachment.

Success response body:

{
  "id":"3",
  "url":"http://localhost:8182/runtime/tasks/2/attachments/3",
  "name":"Simple attachment",
  "description":"Simple attachment description",
  "type":"simpleType",
  "taskUrl":"http://localhost:8182/runtime/tasks/2",
  "processInstanceUrl":null,
  "externalUrl":"http://activiti.org",
  "contentUrl":null
}
Table 15.157. Create a new attachment on a task, containing a link to an external resource - Response codes
Response code	Description
201	Indicates the attachment was created and the result is returned.
400	Indicates the attachment name is missing from the request.
404	Indicates the requested task was not found.


EOF
        ],
        [
            <<<EOF
            Create a new attachment on a task, with an attached file
EOF
            , 'POST runtime/tasks/{taskId}/attachments', 'POST', 'runtime/tasks/{taskId}/attachments',
            <<<EOF

Table 15.158. Create a new attachment on a task, with an attached file - URL parameters
Parameter	Required	Value	Description
taskId	Yes	String	The id of the task to create the attachment for.

Request body: The request should be of type multipart/form-data. There should be a single file-part included with the binary value of the variable. On top of that, the following additional form-fields can be present:

name: Required name of the variable.
description: Description of the attachment, optional.
type: Type of attachment, optional. Supports any arbitrary string or a valid HTTP content-type.
Success response body:

{
      "id":"5",
      "url":"http://localhost:8182/runtime/tasks/2/attachments/5",
      "name":"Binary attachment",
      "description":"Binary attachment description",
      "type":"binaryType",
      "taskUrl":"http://localhost:8182/runtime/tasks/2",
      "processInstanceUrl":null,
      "externalUrl":null,
      "contentUrl":"http://localhost:8182/runtime/tasks/2/attachments/5/content"
   }
Table 15.159. Create a new attachment on a task, with an attached file - Response codes
Response code	Description
201	Indicates the attachment was created and the result is returned.
400	Indicates the attachment name is missing from the request or no file was present in the request. The error-message contains additional information.
404	Indicates the requested task was not found.


EOF
        ],
        [
            <<<EOF
            Get all attachments on a task
EOF
            , 'GET runtime/tasks/{taskId}/attachments', 'GET', 'runtime/tasks/{taskId}/attachments',
            <<<EOF

Table 15.160. Get all attachments on a task - URL parameters
Parameter	Required	Value	Description
taskId	Yes	String	The id of the task to get the attachments for.

Success response body:

[
  {
    "id":"3",
    "url":"http://localhost:8182/runtime/tasks/2/attachments/3",
    "name":"Simple attachment",
    "description":"Simple attachment description",
    "type":"simpleType",
    "taskUrl":"http://localhost:8182/runtime/tasks/2",
    "processInstanceUrl":null,
    "externalUrl":"http://activiti.org",
    "contentUrl":null
  },
  {
    "id":"5",
    "url":"http://localhost:8182/runtime/tasks/2/attachments/5",
    "name":"Binary attachment",
    "description":"Binary attachment description",
    "type":"binaryType",
    "taskUrl":"http://localhost:8182/runtime/tasks/2",
    "processInstanceUrl":null,
    "externalUrl":null,
    "contentUrl":"http://localhost:8182/runtime/tasks/2/attachments/5/content"
  }
]
Table 15.161. Get all attachments on a task - Response codes
Response code	Description
200	Indicates the task was found and the attachments are returned.
404	Indicates the requested task was not found.


EOF
        ],
        [
            <<<EOF
            Get an attachment on a task
EOF
            , 'GET runtime/tasks/{taskId}/attachments/{attachmentId}', 'GET', 'runtime/tasks/{taskId}/attachments/{attachmentId}',
            <<<EOF

Table 15.162. Get an attachment on a task - URL parameters
Parameter	Required	Value	Description
taskId	Yes	String	The id of the task to get the attachment for.
attachmentId	Yes	String	The id of the attachment.

Success response body:

  {
    "id":"5",
    "url":"http://localhost:8182/runtime/tasks/2/attachments/5",
    "name":"Binary attachment",
    "description":"Binary attachment description",
    "type":"binaryType",
    "taskUrl":"http://localhost:8182/runtime/tasks/2",
    "processInstanceUrl":null,
    "externalUrl":null,
    "contentUrl":"http://localhost:8182/runtime/tasks/2/attachments/5/content"
  }
externalUrl - contentUrl:In case the attachment is a link to an external resource, the externalUrl contains the URL to the external content. If the attachment content is present in the Activiti engine, the contentUrl will contain an URL where the binary content can be streamed from.
type:Can be any arbitrary value. When a valid formatted media-type (eg. application/xml, text/plain) is included, the binary content HTTP response content-type will be set the the given value.
Table 15.163. Get an attachment on a task - Response codes
Response code	Description
200	Indicates the task and attachment were found and the attachment is returned.
404	Indicates the requested task was not found or the tasks doesn't have a attachment with the given ID.


EOF
        ],
        [
            <<<EOF
            Get the content for an attachment
EOF
            , 'GET runtime/tasks/{taskId}/attachment/{attachmentId}/content', 'GET', 'runtime/tasks/{taskId}/attachment/{attachmentId}/content',
            <<<EOF

Table 15.164. Get the content for an attachment - URL parameters
Parameter	Required	Value	Description
taskId	Yes	String	The id of the task to get a variable data for.
attachmentId	Yes	String	The id of the attachment, a 404 is returned when the attachment points to an external URL rather than content attached in Activiti.

Table 15.165. Get the content for an attachment - Response codes
Response code	Description
200	Indicates the task and attachment was found and the requested content is returned.
404	Indicates the requested task was not found or the task doesn't have an attachment with the given id or the attachment doesn't have a binary stream available. Status message provides additional information.

Success response body: The response body contains the binary content. By default, the content-type of the response is set to application/octet-stream unless the attachment type contains a valid Content-type.


EOF
        ],
        [
            <<<EOF
            Delete an attachment on a task
EOF
            , 'DELETE runtime/tasks/{taskId}/attachments/{attachmentId}', 'DELETE', 'runtime/tasks/{taskId}/attachments/{attachmentId}',
            <<<EOF

Table 15.166. Delete an attachment on a task - URL parameters
Parameter	Required	Value	Description
taskId	Yes	String	The id of the task to delete the attachment for.
attachmentId	Yes	String	The id of the attachment.

Table 15.167. Delete an attachment on a task - Response codes
Response code	Description
204	Indicates the task and attachment were found and the attachment is deleted. Response body is left empty intentionally.
404	Indicates the requested task was not found or the tasks doesn't have a attachment with the given ID.

History

EOF
        ],
        [
            <<<EOF
            Get a historic process instance
EOF
            , 'GET history/historic-process-instances/{processInstanceId}', 'GET', 'history/historic-process-instances/{processInstanceId}',
            <<<EOF

Table 15.168. Get a historic process instance - Response codes
Response code	Description
200	Indicates that the historic process instances could be found.
404	Indicates that the historic process instances could not be found.

Success response body:

{
  "data": [
    {
      "id" : "5",
      "businessKey" : "myKey",
      "processDefinitionId" : "oneTaskProcess%3A1%3A4",
      "processDefinitionUrl" : "http://localhost:8182/repository/process-definitions/oneTaskProcess%3A1%3A4",
      "startTime" : "2013-04-17T10:17:43.902+0000",
      "endTime" : "2013-04-18T14:06:32.715+0000",
      "durationInMillis" : 86400056,
      "startUserId" : "kermit",
      "startActivityId" : "startEvent",
      "endActivityId" : "endEvent",
      "deleteReason" : null,
      "superProcessInstanceId" : "3",
      "url" : "http://localhost:8182/history/historic-process-instances/5",
      "variables": null,
      "tenantId":null
    }
  ],
  "total": 1,
  "start": 0,
  "sort": "name",
  "order": "asc",
  "size": 1
}

EOF
        ],
        [
            <<<EOF
            List of historic process instances
EOF
            , 'GET history/historic-process-instances', 'GET', 'history/historic-process-instances',
            <<<EOF

Table 15.169. List of historic process instances - URL parameters
Parameter	Required	Value	Description
processInstanceId	No	String	An id of the historic process instance.
processDefinitionKey	No	String	The process definition key of the historic process instance.
processDefinitionId	No	String	The process definition id of the historic process instance.
businessKey	No	String	The business key of the historic process instance.
involvedUser	No	String	An involved user of the historic process instance.
finished	No	Boolean	Indication if the historic process instance is finished.
superProcessInstanceId	No	String	An optional parent process id of the historic process instance.
excludeSubprocesses	No	Boolean	Return only historic process instances which aren't sub processes.
finishedAfter	No	Date	Return only historic process instances that were finished after this date.
finishedBefore	No	Date	Return only historic process instances that were finished before this date.
startedAfter	No	Date	Return only historic process instances that were started after this date.
startedBefore	No	Date	Return only historic process instances that were started before this date.
startedBy	No	String	Return only historic process instances that were started by this user.
includeProcessVariables	No	Boolean	An indication if the historic process instance variables should be returned as well.
tenantId	No	String	Only return instances with the given tenantId.
tenantIdLike	No	String	Only return instances with a tenantId like the given value.
withoutTenantId	No	Boolean	If true, only returns instances without a tenantId set. If false, the withoutTenantId parameter is ignored.
The general paging and sorting query-parameters can be used for this URL.



Table 15.170. List of historic process instances - Response codes
Response code	Description
200	Indicates that historic process instances could be queried.
400	Indicates an parameter was passed in the wrong format. The status-message contains additional information.

Success response body:

{
  "data": [
    {
      "id" : "5",
      "businessKey" : "myKey",
      "processDefinitionId" : "oneTaskProcess%3A1%3A4",
      "processDefinitionUrl" : "http://localhost:8182/repository/process-definitions/oneTaskProcess%3A1%3A4",
      "startTime" : "2013-04-17T10:17:43.902+0000",
      "endTime" : "2013-04-18T14:06:32.715+0000",
      "durationInMillis" : 86400056,
      "startUserId" : "kermit",
      "startActivityId" : "startEvent",
      "endActivityId" : "endEvent",
      "deleteReason" : null,
      "superProcessInstanceId" : "3",
      "url" : "http://localhost:8182/history/historic-process-instances/5",
      "variables": [
        {
          "name": "test",
          "variableScope": "local",
          "value": "myTest"
        }
      ],
      "tenantId":null
    }
  ],
  "total": 1,
  "start": 0,
  "sort": "name",
  "order": "asc",
  "size": 1
}

EOF
        ],
        [
            <<<EOF
            Query for historic process instances
EOF
            , 'POST query/historic-process-instances', 'POST', 'query/historic-process-instances',
            <<<EOF

Request body:

{
  "processDefinitionId" : "oneTaskProcess%3A1%3A4",
  ...

  "variables" : [
    {
      "name" : "myVariable",
      "value" : 1234,
      "operation" : "equals",
      "type" : "long"
    }
  ]
}
All supported JSON parameter fields allowed are exactly the same as the parameters found for getting a collection of historic process instances, but passed in as JSON-body arguments rather than URL-parameters to allow for more advanced querying and preventing errors with request-uri's that are too long. On top of that, the query allows for filtering based on process variables. The variables property is a json-array containing objects with the format as described here.

Table 15.171. Query for historic process instances - Response codes
Response code	Description
200	Indicates request was successful and the tasks are returned
400	Indicates an parameter was passed in the wrong format. The status-message contains additional information.

Success response body:

{
  "data": [
    {
      "id" : "5",
      "businessKey" : "myKey",
      "processDefinitionId" : "oneTaskProcess%3A1%3A4",
      "processDefinitionUrl" : "http://localhost:8182/repository/process-definitions/oneTaskProcess%3A1%3A4",
      "startTime" : "2013-04-17T10:17:43.902+0000",
      "endTime" : "2013-04-18T14:06:32.715+0000",
      "durationInMillis" : 86400056,
      "startUserId" : "kermit",
      "startActivityId" : "startEvent",
      "endActivityId" : "endEvent",
      "deleteReason" : null,
      "superProcessInstanceId" : "3",
      "url" : "http://localhost:8182/history/historic-process-instances/5",
      "variables": [
        {
          "name": "test",
          "variableScope": "local",
          "value": "myTest"
        }
      ],
      "tenantId":null
    }
  ],
  "total": 1,
  "start": 0,
  "sort": "name",
  "order": "asc",
  "size": 1
}

EOF
        ],
        [
            <<<EOF
            Delete a historic process instance
EOF
            , 'DELETE history/historic-process-instances/{processInstanceId}', 'DELETE', 'history/historic-process-instances/{processInstanceId}',
            <<<EOF

Table 15.172. Response codes
Response code	Description
200	Indicates that the historic process instance was deleted.
404	Indicates that the historic process instance could not be found.


EOF
        ],
        [
            <<<EOF
            Get the identity links of a historic process instance
EOF
            , 'GET history/historic-process-instance/{processInstanceId}/identitylinks', 'GET', 'history/historic-process-instance/{processInstanceId}/identitylinks',
            <<<EOF

Table 15.173. Response codes
Response code	Description
200	Indicates request was successful and the identity links are returned
404	Indicates the process instance could not be found.

Success response body:

[
 {
  "type" : "participant",
  "userId" : "kermit",
  "groupId" : null,
  "taskId" : null,
  "taskUrl" : null,
  "processInstanceId" : "5",
  "processInstanceUrl" : "http://localhost:8182/history/historic-process-instances/5"
 }
]

EOF
        ],
        [
            <<<EOF
            Get the binary data for a historic process instance variable
EOF
            , 'GET history/historic-process-instances/{processInstanceId}/variables/{variableName}/data', 'GET', 'history/historic-process-instances/{processInstanceId}/variables/{variableName}/data',
            <<<EOF

Table 15.174. Get the binary data for a historic process instance variable - Response codes
Response code	Description
200	Indicates the process instance was found and the requested variable data is returned.
404	Indicates the requested process instance was not found or the process instance doesn't have a variable with the given name or the variable doesn't have a binary stream available. Status message provides additional information.

Success response body: The response body contains the binary value of the variable. When the variable is of type binary, the content-type of the response is set to application/octet-stream, regardless of the content of the variable or the request accept-type header. In case of serializable, application/x-java-serialized-object is used as content-type.


EOF
        ],
        [
            <<<EOF
            Create a new comment on a historic process instance
EOF
            , 'POST history/historic-process-instances/{processInstanceId}/comments', 'POST', 'history/historic-process-instances/{processInstanceId}/comments',
            <<<EOF

Table 15.175. Create a new comment on a historic process instance - URL parameters
Parameter	Required	Value	Description
processInstanceId	Yes	String	The id of the process instance to create the comment for.

Request body:

{
  "message" : "This is a comment.",
  "saveProcessInstanceId" : true
}
Parameter saveProcessInstanceId is optional, if true save process instance id of task with comment.

Success response body:

{
  "id" : "123",
  "taskUrl" : "http://localhost:8081/activiti-rest/service/runtime/tasks/101/comments/123",
  "processInstanceUrl" : "http://localhost:8081/activiti-rest/service/history/historic-process-instances/100/comments/123",
  "message" : "This is a comment on the task.",
  "author" : "kermit",
  "time" : "2014-07-13T13:13:52.232+08:00",
  "taskId" : "101",
  "processInstanceId" : "100"
}
Table 15.176. Create a new comment on a historic process instance - Response codes
Response code	Description
201	Indicates the comment was created and the result is returned.
400	Indicates the comment is missing from the request.
404	Indicates the requested historic process instance was not found.


EOF
        ],
        [
            <<<EOF
            Get all comments on a historic process instance
EOF
            , 'GET history/historic-process-instances/{processInstanceId}/comments', 'GET', 'history/historic-process-instances/{processInstanceId}/comments',
            <<<EOF

Table 15.177. Get all comments on a process instance - URL parameters
Parameter	Required	Value	Description
processInstanceId	Yes	String	The id of the process instance to get the comments for.

Success response body:

[
  {
    "id" : "123",
    "processInstanceUrl" : "http://localhost:8081/activiti-rest/service/history/historic-process-instances/100/comments/123",
    "message" : "This is a comment on the task.",
    "author" : "kermit",
    "time" : "2014-07-13T13:13:52.232+08:00",
    "processInstanceId" : "100"
  },
  {
    "id" : "456",
    "processInstanceUrl" : "http://localhost:8081/activiti-rest/service/history/historic-process-instances/100/comments/456",
    "message" : "This is another comment.",
    "author" : "gonzo",
    "time" : "2014-07-14T15:16:52.232+08:00",
    "processInstanceId" : "100"
  }
]
Table 15.178. Get all comments on a process instance - Response codes
Response code	Description
200	Indicates the process instance was found and the comments are returned.
404	Indicates the requested task was not found.


EOF
        ],
        [
            <<<EOF
            Get a comment on a historic process instance
EOF
            , 'GET history/historic-process-instances/{processInstanceId}/comments/{commentId}', 'GET', 'history/historic-process-instances/{processInstanceId}/comments/{commentId}',
            <<<EOF

Table 15.179. Get a comment on a historic process instance - URL parameters
Parameter	Required	Value	Description
processInstanceId	Yes	String	The id of the historic process instance to get the comment for.
commentId	Yes	String	The id of the comment.

Success response body:

{
  "id" : "123",
  "processInstanceUrl" : "http://localhost:8081/activiti-rest/service/history/historic-process-instances/100/comments/456",
  "message" : "This is another comment.",
  "author" : "gonzo",
  "time" : "2014-07-14T15:16:52.232+08:00",
  "processInstanceId" : "100"
}
Table 15.180. Get a comment on a historic process instance - Response codes
Response code	Description
200	Indicates the historic process instance and comment were found and the comment is returned.
404	Indicates the requested historic process instance was not found or the historic process instance doesn't have a comment with the given ID.


EOF
        ],
        [
            <<<EOF
            Delete a comment on a historic process instance
EOF
            , 'DELETE history/historic-process-instances/{processInstanceId}/comments/{commentId}', 'DELETE', 'history/historic-process-instances/{processInstanceId}/comments/{commentId}',
            <<<EOF

Table 15.181. Delete a comment on a historic process instance - URL parameters
Parameter	Required	Value	Description
processInstanceId	Yes	String	The id of the historic process instance to delete the comment for.
commentId	Yes	String	The id of the comment.

Table 15.182. Delete a comment on a historic process instance - Response codes
Response code	Description
204	Indicates the historic process instance and comment were found and the comment is deleted. Response body is left empty intentionally.
404	Indicates the requested task was not found or the historic process instance doesn't have a comment with the given ID.


EOF
        ],
        [
            <<<EOF
            Get a single historic task instance
EOF
            , 'GET history/historic-task-instances/{taskId}', 'GET', 'history/historic-task-instances/{taskId}',
            <<<EOF

Table 15.183. Get a single historic task instance - Response codes
Response code	Description
200	Indicates that the historic task instances could be found.
404	Indicates that the historic task instances could not be found.

Success response body:

{
  "id" : "5",
  "processDefinitionId" : "oneTaskProcess%3A1%3A4",
  "processDefinitionUrl" : "http://localhost:8182/repository/process-definitions/oneTaskProcess%3A1%3A4",
  "processInstanceId" : "3",
  "processInstanceUrl" : "http://localhost:8182/history/historic-process-instances/3",
  "executionId" : "4",
  "name" : "My task name",
  "description" : "My task description",
  "deleteReason" : null,
  "owner" : "kermit",
  "assignee" : "fozzie",
  "startTime" : "2013-04-17T10:17:43.902+0000",
  "endTime" : "2013-04-18T14:06:32.715+0000",
  "durationInMillis" : 86400056,
  "workTimeInMillis" : 234890,
  "claimTime" : "2013-04-18T11:01:54.715+0000",
  "taskDefinitionKey" : "taskKey",
  "formKey" : null,
  "priority" : 50,
  "dueDate" : "2013-04-20T12:11:13.134+0000",
  "parentTaskId" : null,
  "url" : "http://localhost:8182/history/historic-task-instances/5",
  "variables": null,
  "tenantId":null
}

EOF
        ],
        [
            <<<EOF
            Get historic task instances
EOF
            , 'GET history/historic-task-instances', 'GET', 'history/historic-task-instances',
            <<<EOF

Table 15.184. Get historic task instances - URL parameters
Parameter	Required	Value	Description
taskId	No	String	An id of the historic task instance.
processInstanceId	No	String	The process instance id of the historic task instance.
processDefinitionKey	No	String	The process definition key of the historic task instance.
processDefinitionKeyLike	No	String	The process definition key of the historic task instance, which matches the given value.
processDefinitionId	No	String	The process definition id of the historic task instance.
processDefinitionName	No	String	The process definition name of the historic task instance.
processDefinitionNameLike	No	String	The process definition name of the historic task instance, which matches the given value.
processBusinessKey	No	String	The process instance business key of the historic task instance.
processBusinessKeyLike	No	String	The process instance business key of the historic task instance that matches the given value.
executionId	No	String	The execution id of the historic task instance.
taskDefinitionKey	No	String	The task definition key for tasks part of a process
taskName	No	String	The task name of the historic task instance.
taskNameLike	No	String	The task name with 'like' operator for the historic task instance.
taskDescription	No	String	The task description of the historic task instance.
taskDescriptionLike	No	String	The task description with 'like' operator for the historic task instance.
taskDefinitionKey	No	String	The task identifier from the process definition for the historic task instance.
taskDeleteReason	No	String	The task delete reason of the historic task instance.
taskDeleteReasonLike	No	String	The task delete reason with 'like' operator for the historic task instance.
taskAssignee	No	String	The assignee of the historic task instance.
taskAssigneeLike	No	String	The assignee with 'like' operator for the historic task instance.
taskOwner	No	String	The owner of the historic task instance.
taskOwnerLike	No	String	The owner with 'like' operator for the historic task instance.
taskInvolvedUser	No	String	An involved user of the historic task instance.
taskPriority	No	String	The priority of the historic task instance.
finished	No	Boolean	Indication if the historic task instance is finished.
processFinished	No	Boolean	Indication if the process instance of the historic task instance is finished.
parentTaskId	No	String	An optional parent task id of the historic task instance.
dueDate	No	Date	Return only historic task instances that have a due date equal this date.
dueDateAfter	No	Date	Return only historic task instances that have a due date after this date.
dueDateBefore	No	Date	Return only historic task instances that have a due date before this date.
withoutDueDate	No	Boolean	Return only historic task instances that have no due-date. When false is provided as value, this parameter is ignored.
taskCompletedOn	No	Date	Return only historic task instances that have been completed on this date.
taskCompletedAfter	No	Date	Return only historic task instances that have been completed after this date.
taskCompletedBefore	No	Date	Return only historic task instances that have been completed before this date.
taskCreatedOn	No	Date	Return only historic task instances that were created on this date.
taskCreatedBefore	No	Date	Return only historic task instances that were created before this date.
taskCreatedAfter	No	Date	Return only historic task instances that were created after this date.
includeTaskLocalVariables	No	Boolean	An indication if the historic task instance local variables should be returned as well.
includeProcessVariables	No	Boolean	An indication if the historic task instance global variables should be returned as well.
tenantId	No	String	Only return historic task instances with the given tenantId.
tenantIdLike	No	String	Only return historic task instances with a tenantId like the given value.
withoutTenantId	No	Boolean	If true, only returns historic task instances without a tenantId set. If false, the withoutTenantId parameter is ignored.
The general paging and sorting query-parameters can be used for this URL.



Table 15.185. Get historic task instances - Response codes
Response code	Description
200	Indicates that historic process instances could be queried.
400	Indicates an parameter was passed in the wrong format. The status-message contains additional information.

Success response body:

{
  "data": [
    {
      "id" : "5",
      "processDefinitionId" : "oneTaskProcess%3A1%3A4",
      "processDefinitionUrl" : "http://localhost:8182/repository/process-definitions/oneTaskProcess%3A1%3A4",
      "processInstanceId" : "3",
      "processInstanceUrl" : "http://localhost:8182/history/historic-process-instances/3",
      "executionId" : "4",
      "name" : "My task name",
      "description" : "My task description",
      "deleteReason" : null,
      "owner" : "kermit",
      "assignee" : "fozzie",
      "startTime" : "2013-04-17T10:17:43.902+0000",
      "endTime" : "2013-04-18T14:06:32.715+0000",
      "durationInMillis" : 86400056,
      "workTimeInMillis" : 234890,
      "claimTime" : "2013-04-18T11:01:54.715+0000",
      "taskDefinitionKey" : "taskKey",
      "formKey" : null,
      "priority" : 50,
      "dueDate" : "2013-04-20T12:11:13.134+0000",
      "parentTaskId" : null,
      "url" : "http://localhost:8182/history/historic-task-instances/5",
      "taskVariables": [
        {
          "name": "test",
          "variableScope": "local",
          "value": "myTest"
        }
      ],
      "processVariables": [
        {
          "name": "processTest",
          "variableScope": "global",
          "value": "myProcessTest"
        }
      ],
      "tenantId":null
    }
  ],
  "total": 1,
  "start": 0,
  "sort": "name",
  "order": "asc",
  "size": 1
}

EOF
        ],
        [
            <<<EOF
            Query for historic task instances
EOF
            , 'POST query/historic-task-instances', 'POST', 'query/historic-task-instances',
            <<<EOF

Query for historic task instances - Request body:

{
  "processDefinitionId" : "oneTaskProcess%3A1%3A4",
  ...

  "variables" : [
    {
      "name" : "myVariable",
      "value" : 1234,
      "operation" : "equals",
      "type" : "long"
    }
  ]
}
All supported JSON parameter fields allowed are exactly the same as the parameters found for getting a collection of historic task instances, but passed in as JSON-body arguments rather than URL-parameters to allow for more advanced querying and preventing errors with request-uri's that are too long. On top of that, the query allows for filtering based on process variables. The taskVariables and processVariables properties are json-arrays containing objects with the format as described here.

Table 15.186. Query for historic task instances - Response codes
Response code	Description
200	Indicates request was successful and the tasks are returned
400	Indicates an parameter was passed in the wrong format. The status-message contains additional information.

Success response body:

{
  "data": [
    {
      "id" : "5",
      "processDefinitionId" : "oneTaskProcess%3A1%3A4",
      "processDefinitionUrl" : "http://localhost:8182/repository/process-definitions/oneTaskProcess%3A1%3A4",
      "processInstanceId" : "3",
      "processInstanceUrl" : "http://localhost:8182/history/historic-process-instances/3",
      "executionId" : "4",
      "name" : "My task name",
      "description" : "My task description",
      "deleteReason" : null,
      "owner" : "kermit",
      "assignee" : "fozzie",
      "startTime" : "2013-04-17T10:17:43.902+0000",
      "endTime" : "2013-04-18T14:06:32.715+0000",
      "durationInMillis" : 86400056,
      "workTimeInMillis" : 234890,
      "claimTime" : "2013-04-18T11:01:54.715+0000",
      "taskDefinitionKey" : "taskKey",
      "formKey" : null,
      "priority" : 50,
      "dueDate" : "2013-04-20T12:11:13.134+0000",
      "parentTaskId" : null,
      "url" : "http://localhost:8182/history/historic-task-instances/5",
      "taskVariables": [
        {
          "name": "test",
          "variableScope": "local",
          "value": "myTest"
        }
      ],
      "processVariables": [
        {
          "name": "processTest",
          "variableScope": "global",
          "value": "myProcessTest"
        }
      ],
      "tenantId":null
    }
  ],
  "total": 1,
  "start": 0,
  "sort": "name",
  "order": "asc",
  "size": 1
}

EOF
        ],
        [
            <<<EOF
            Delete a historic task instance
EOF
            , 'DELETE history/historic-task-instances/{taskId}', 'DELETE', 'history/historic-task-instances/{taskId}',
            <<<EOF

Table 15.187. Response codes
Response code	Description
200	Indicates that the historic task instance was deleted.
404	Indicates that the historic task instance could not be found.


EOF
        ],
        [
            <<<EOF
            Get the identity links of a historic task instance
EOF
            , 'GET history/historic-task-instance/{taskId}/identitylinks', 'GET', 'history/historic-task-instance/{taskId}/identitylinks',
            <<<EOF

Table 15.188. Response codes
Response code	Description
200	Indicates request was successful and the identity links are returned
404	Indicates the task instance could not be found.

Success response body:

[
 {
  "type" : "assignee",
  "userId" : "kermit",
  "groupId" : null,
  "taskId" : "6",
  "taskUrl" : "http://localhost:8182/history/historic-task-instances/5",
  "processInstanceId" : null,
  "processInstanceUrl" : null
 }
]

EOF
        ],
        [
            <<<EOF
            Get the binary data for a historic task instance variable
EOF
            , 'GET history/historic-task-instances/{taskId}/variables/{variableName}/data', 'GET', 'history/historic-task-instances/{taskId}/variables/{variableName}/data',
            <<<EOF

Table 15.189. Get the binary data for a historic task instance variable - Response codes
Response code	Description
200	Indicates the task instance was found and the requested variable data is returned.
404	Indicates the requested task instance was not found or the process instance doesn't have a variable with the given name or the variable doesn't have a binary stream available. Status message provides additional information.

Success response body: The response body contains the binary value of the variable. When the variable is of type binary, the content-type of the response is set to application/octet-stream, regardless of the content of the variable or the request accept-type header. In case of serializable, application/x-java-serialized-object is used as content-type.


EOF
        ],
        [
            <<<EOF
            Get historic activity instances
EOF
            , 'GET history/historic-activity-instances', 'GET', 'history/historic-activity-instances',
            <<<EOF

Table 15.190. Get historic activity instances - URL parameters
Parameter	Required	Value	Description
activityId	No	String	An id of the activity instance.
activityInstanceId	No	String	An id of the historic activity instance.
activityName	No	String	The name of the historic activity instance.
activityType	No	String	The element type of the historic activity instance.
executionId	No	String	The execution id of the historic activity instance.
finished	No	Boolean	Indication if the historic activity instance is finished.
taskAssignee	No	String	The assignee of the historic activity instance.
processInstanceId	No	String	The process instance id of the historic activity instance.
processDefinitionId	No	String	The process definition id of the historic activity instance.
tenantId	No	String	Only return instances with the given tenantId.
tenantIdLike	No	String	Only return instances with a tenantId like the given value.
withoutTenantId	No	Boolean	If true, only returns instances without a tenantId set. If false, the withoutTenantId parameter is ignored.
The general paging and sorting query-parameters can be used for this URL.



Table 15.191. Get historic activity instances - Response codes
Response code	Description
200	Indicates that historic activity instances could be queried.
400	Indicates an parameter was passed in the wrong format. The status-message contains additional information.

Success response body:

{
  "data": [
    {
      "id" : "5",
      "activityId" : "4",
      "activityName" : "My user task",
      "activityType" : "userTask",
      "processDefinitionId" : "oneTaskProcess%3A1%3A4",
      "processDefinitionUrl" : "http://localhost:8182/repository/process-definitions/oneTaskProcess%3A1%3A4",
      "processInstanceId" : "3",
      "processInstanceUrl" : "http://localhost:8182/history/historic-process-instances/3",
      "executionId" : "4",
      "taskId" : "4",
      "calledProcessInstanceId" : null,
      "assignee" : "fozzie",
      "startTime" : "2013-04-17T10:17:43.902+0000",
      "endTime" : "2013-04-18T14:06:32.715+0000",
      "durationInMillis" : 86400056,
      "tenantId":null
    }
  ],
  "total": 1,
  "start": 0,
  "sort": "name",
  "order": "asc",
  "size": 1
}

EOF
        ],
        [
            <<<EOF
            Query for historic activity instances
EOF
            , 'POST query/historic-activity-instances', 'POST', 'query/historic-activity-instances',
            <<<EOF

Request body:

{
  "processDefinitionId" : "oneTaskProcess%3A1%3A4"
}
All supported JSON parameter fields allowed are exactly the same as the parameters found for getting a collection of historic task instances, but passed in as JSON-body arguments rather than URL-parameters to allow for more advanced querying and preventing errors with request-uri's that are too long.

Table 15.192. Query for historic activity instances - Response codes
Response code	Description
200	Indicates request was successful and the activities are returned
400	Indicates an parameter was passed in the wrong format. The status-message contains additional information.

Success response body:

{
  "data": [
    {
      "id" : "5",
      "activityId" : "4",
      "activityName" : "My user task",
      "activityType" : "userTask",
      "processDefinitionId" : "oneTaskProcess%3A1%3A4",
      "processDefinitionUrl" : "http://localhost:8182/repository/process-definitions/oneTaskProcess%3A1%3A4",
      "processInstanceId" : "3",
      "processInstanceUrl" : "http://localhost:8182/history/historic-process-instances/3",
      "executionId" : "4",
      "taskId" : "4",
      "calledProcessInstanceId" : null,
      "assignee" : "fozzie",
      "startTime" : "2013-04-17T10:17:43.902+0000",
      "endTime" : "2013-04-18T14:06:32.715+0000",
      "durationInMillis" : 86400056,
      "tenantId":null
    }
  ],
  "total": 1,
  "start": 0,
  "sort": "name",
  "order": "asc",
  "size": 1
}

EOF
        ],
        [
            <<<EOF
            List of historic variable instances
EOF
            , 'GET history/historic-variable-instances', 'GET', 'history/historic-variable-instances',
            <<<EOF

Table 15.193. List of historic variable instances - URL parameters
Parameter	Required	Value	Description
processInstanceId	No	String	The process instance id of the historic variable instance.
taskId	No	String	The task id of the historic variable instance.
excludeTaskVariables	No	Boolean	Indication to exclude the task variables from the result.
variableName	No	String	The variable name of the historic variable instance.
variableNameLike	No	String	The variable name using the 'like' operator for the historic variable instance.
The general paging and sorting query-parameters can be used for this URL.



Table 15.194. List of historic variable instances - Response codes
Response code	Description
200	Indicates that historic variable instances could be queried.
400	Indicates an parameter was passed in the wrong format. The status-message contains additional information.

Success response body:

{
  "data": [
    {
      "id" : "14",
      "processInstanceId" : "5",
      "processInstanceUrl" : "http://localhost:8182/history/historic-process-instances/5",
      "taskId" : "6",
      "variable" : {
        "name" : "myVariable",
        "variableScope", "global",
        "value" : "test"
      }
    }
  ],
  "total": 1,
  "start": 0,
  "sort": "name",
  "order": "asc",
  "size": 1
}

EOF
        ],
        [
            <<<EOF
            Query for historic variable instances
EOF
            , 'POST query/historic-variable-instances', 'POST', 'query/historic-variable-instances',
            <<<EOF

Request body:

{
  "processDefinitionId" : "oneTaskProcess%3A1%3A4",
  ...

  "variables" : [
    {
      "name" : "myVariable",
      "value" : 1234,
      "operation" : "equals",
      "type" : "long"
    }
  ]
}
All supported JSON parameter fields allowed are exactly the same as the parameters found for getting a collection of historic process instances, but passed in as JSON-body arguments rather than URL-parameters to allow for more advanced querying and preventing errors with request-uri's that are too long. On top of that, the query allows for filtering based on process variables. The variables property is a json-array containing objects with the format as described here.

Table 15.195. Query for historic variable instances - Response codes
Response code	Description
200	Indicates request was successful and the tasks are returned
400	Indicates an parameter was passed in the wrong format. The status-message contains additional information.

Success response body:

{
  "data": [
    {
      "id" : "14",
      "processInstanceId" : "5",
      "processInstanceUrl" : "http://localhost:8182/history/historic-process-instances/5",
      "taskId" : "6",
      "variable" : {
        "name" : "myVariable",
        "variableScope", "global",
        "value" : "test"
      }
    }
  ],
  "total": 1,
  "start": 0,
  "sort": "name",
  "order": "asc",
  "size": 1
}

EOF
        ],
        [
            <<<EOF
            Get the binary data for a historic task instance variable
EOF
            , 'GET history/historic-variable-instances/{varInstanceId}/data', 'GET', 'history/historic-variable-instances/{varInstanceId}/data',
            <<<EOF

Table 15.196. Get the binary data for a historic task instance variable - Response codes
Response code	Description
200	Indicates the variable instance was found and the requested variable data is returned.
404	Indicates the requested variable instance was not found or the variable instance doesn't have a variable with the given name or the variable doesn't have a binary stream available. Status message provides additional information.

Success response body: The response body contains the binary value of the variable. When the variable is of type binary, the content-type of the response is set to application/octet-stream, regardless of the content of the variable or the request accept-type header. In case of serializable, application/x-java-serialized-object is used as content-type.


EOF
        ],
        [
            <<<EOF
            Get historic detail
EOF
            , 'GET history/historic-detail', 'GET', 'history/historic-detail',
            <<<EOF

Table 15.197. Get historic detail - URL parameters
Parameter	Required	Value	Description
id	No	String	The id of the historic detail.
processInstanceId	No	String	The process instance id of the historic detail.
executionId	No	String	The execution id of the historic detail.
activityInstanceId	No	String	The activity instance id of the historic detail.
taskId	No	String	The task id of the historic detail.
selectOnlyFormProperties	No	Boolean	Indication to only return form properties in the result.
selectOnlyVariableUpdates	No	Boolean	Indication to only return variable updates in the result.
The general paging and sorting query-parameters can be used for this URL.



Table 15.198. Get historic detail - Response codes
Response code	Description
200	Indicates that historic detail could be queried.
400	Indicates an parameter was passed in the wrong format. The status-message contains additional information.

Success response body:

{
  "data": [
    {
      "id" : "26",
      "processInstanceId" : "5",
      "processInstanceUrl" : "http://localhost:8182/history/historic-process-instances/5",
      "executionId" : "6",
      "activityInstanceId", "10",
      "taskId" : "6",
      "taskUrl" : "http://localhost:8182/history/historic-task-instances/6",
      "time" : "2013-04-17T10:17:43.902+0000",
      "detailType" : "variableUpdate",
      "revision" : 2,
      "variable" : {
        "name" : "myVariable",
        "variableScope", "global",
        "value" : "test"
      },
      "propertyId", null,
      "propertyValue", null
    }
  ],
  "total": 1,
  "start": 0,
  "sort": "name",
  "order": "asc",
  "size": 1
}

EOF
        ],
        [
            <<<EOF
            Query for historic details
EOF
            , 'POST query/historic-detail', 'POST', 'query/historic-detail',
            <<<EOF

Request body:

{
  "processInstanceId" : "5",
}
All supported JSON parameter fields allowed are exactly the same as the parameters found for getting a collection of historic process instances, but passed in as JSON-body arguments rather than URL-parameters to allow for more advanced querying and preventing errors with request-uri's that are too long.

Table 15.199. Query for historic details - Response codes
Response code	Description
200	Indicates request was successful and the historic details are returned
400	Indicates an parameter was passed in the wrong format. The status-message contains additional information.

Success response body:

{
  "data": [
    {
      "id" : "26",
      "processInstanceId" : "5",
      "processInstanceUrl" : "http://localhost:8182/history/historic-process-instances/5",
      "executionId" : "6",
      "activityInstanceId", "10",
      "taskId" : "6",
      "taskUrl" : "http://localhost:8182/history/historic-task-instances/6",
      "time" : "2013-04-17T10:17:43.902+0000",
      "detailType" : "variableUpdate",
      "revision" : 2,
      "variable" : {
        "name" : "myVariable",
        "variableScope", "global",
        "value" : "test"
      },
      "propertyId", null,
      "propertyValue", null
    }
  ],
  "total": 1,
  "start": 0,
  "sort": "name",
  "order": "asc",
  "size": 1
}

EOF
        ],
        [
            <<<EOF
            Get the binary data for a historic detail variable
EOF
            , 'GET history/historic-detail/{detailId}/data', 'GET', 'history/historic-detail/{detailId}/data',
            <<<EOF

Table 15.200. Get the binary data for a historic detail variable - Response codes
Response code	Description
200	Indicates the historic detail instance was found and the requested variable data is returned.
404	Indicates the requested historic detail instance was not found or the historic detail instance doesn't have a variable with the given name or the variable doesn't have a binary stream available. Status message provides additional information.

Success response body: The response body contains the binary value of the variable. When the variable is of type binary, the content-type of the response is set to application/octet-stream, regardless of the content of the variable or the request accept-type header. In case of serializable, application/x-java-serialized-object is used as content-type.

Forms

EOF
        ],
        [
            <<<EOF
            Get form data
EOF
            , 'GET form/form-data', 'GET', 'form/form-data',
            <<<EOF

Table 15.201. Get form data - URL parameters
Parameter	Required	Value	Description
taskId	Yes (if no processDefinitionId)	String	The task id corresponding to the form data that needs to be retrieved.
processDefinitionId	Yes (if no taskId)	String	The process definition id corresponding to the start event form data that needs to be retrieved.

Table 15.202. Get form data - Response codes
Response code	Description
200	Indicates that form data could be queried.
404	Indicates that form data could not be found.

Success response body:

{
  "data": [
    {
      "formKey" : null,
      "deploymentId" : "2",
      "processDefinitionId" : "3",
      "processDefinitionUrl" : "http://localhost:8182/repository/process-definition/3",
      "taskId" : "6",
      "taskUrl" : "http://localhost:8182/runtime/task/6",
      "formProperties" : [
        {
          "id" : "room",
          "name" : "Room",
          "type" : "string",
          "value" : null,
          "readable" : true,
          "writable" : true,
          "required" : true,
          "datePattern" : null,
          "enumValues" : [
            {
              "id" : "normal",
              "name" : "Normal bed"
            },
            {
              "id" : "kingsize",
              "name" : "Kingsize bed"
            },
          ]
        }
      ]
    }
  ],
  "total": 1,
  "start": 0,
  "sort": "name",
  "order": "asc",
  "size": 1
}

EOF
        ],
        [
            <<<EOF
            Submit task form data
EOF
            , 'POST form/form-data', 'POST', 'form/form-data',
            <<<EOF

Request body for task form:

{
  "taskId" : "5",
  "properties" : [
    {
      "id" : "room",
      "value" : "normal"
    }
  ]
}
Request body for start event form:

{
  "processDefinitionId" : "5",
  "businessKey" : "myKey", (optional)
  "properties" : [
    {
      "id" : "room",
      "value" : "normal"
    }
  ]
}
Table 15.203. Submit task form data - Response codes
Response code	Description
200	Indicates request was successful and the form data was submitted
400	Indicates an parameter was passed in the wrong format. The status-message contains additional information.

Success response body for start event form data (no response for task form data):

{
  "id" : "5",
  "url" : "http://localhost:8182/history/historic-process-instances/5",
  "businessKey" : "myKey",
  "suspended", false,
  "processDefinitionId" : "3",
  "processDefinitionUrl" : "http://localhost:8182/repository/process-definition/3",
  "activityId" : "myTask"
}
Database tables

EOF
        ],
        [
            <<<EOF
            List of tables
EOF
            , 'GET management/tables', 'GET', 'management/tables',
            <<<EOF

Table 15.204. List of tables - Response codes
Response code	Description
200	Indicates the request was successful.

Success response body:

[
   {
      "name":"ACT_RU_VARIABLE",
      "url":"http://localhost:8182/management/tables/ACT_RU_VARIABLE",
      "count":4528
   },
   {
      "name":"ACT_RU_EVENT_SUBSCR",
      "url":"http://localhost:8182/management/tables/ACT_RU_EVENT_SUBSCR",
      "count":3
   },

   ...

]

EOF
        ],
        [
            <<<EOF
            Get a single table
EOF
            , 'GET management/tables/{tableName}', 'GET', 'management/tables/{tableName}',
            <<<EOF

Table 15.205. Get a single table - URL parameters
Parameter	Required	Value	Description
tableName	Yes	String	The name of the table to get.

Success response body:

{
      "name":"ACT_RE_PROCDEF",
      "url":"http://localhost:8182/management/tables/ACT_RE_PROCDEF",
      "count":60
}
Table 15.206. Get a single table - Response codes
Response code	Description
200	Indicates the table exists and the table count is returned.
404	Indicates the requested table does not exist.


EOF
        ],
        [
            <<<EOF
            Get column info for a single table
EOF
            , 'GET management/tables/{tableName}/columns', 'GET', 'management/tables/{tableName}/columns',
            <<<EOF

Table 15.207. Get column info for a single table - URL parameters
Parameter	Required	Value	Description
tableName	Yes	String	The name of the table to get.

Success response body:

{
   "tableName":"ACT_RU_VARIABLE",
   "columnNames":[
      "ID_",
      "REV_",
      "TYPE_",
      "NAME_",
      ...
   ],
   "columnTypes":[
      "VARCHAR",
      "INTEGER",
      "VARCHAR",
      "VARCHAR",
      ...
   ]
}
Table 15.208. Get column info for a single table - Response codes
Response code	Description
200	Indicates the table exists and the table column info is returned.
404	Indicates the requested table does not exist.


EOF
        ],
        [
            <<<EOF
            Get row data for a single table
EOF
            , 'GET management/tables/{tableName}/data', 'GET', 'management/tables/{tableName}/data',
            <<<EOF

Table 15.209. Get row data for a single table - URL parameters
Parameter	Required	Value	Description
tableName	Yes	String	The name of the table to get.

Table 15.210. Get row data for a single table - URL query parameters
Parameter	Required	Value	Description
start	No	Integer	Index of the first row to fetch. Defaults to 0.
size	No	Integer	Number of rows to fetch, starting from start. Defaults to 10.
orderAscendingColumn	No	String	Name of the column to sort the resulting rows on, ascending.
orderDescendingColumn	No	String	Name of the column to sort the resulting rows on, descending.

Success response body:

{
  "total":3,
   "start":0,
   "sort":null,
   "order":null,
   "size":3,

   "data":[
      {
         "TASK_ID_":"2",
         "NAME_":"var1",
         "REV_":1,
         "TEXT_":"123",
         "LONG_":123,
         "ID_":"3",
         "TYPE_":"integer"
      },
      ...
   ]

}
Table 15.211. Get row data for a single table - Response codes
Response code	Description
200	Indicates the table exists and the table row data is returned.
404	Indicates the requested table does not exist.

Engine

EOF
        ],
        [
            <<<EOF
            Get engine properties
EOF
            , 'GET management/properties', 'GET', 'management/properties',
            <<<EOF

Returns a read-only view of the properties used internally in the engine.

Success response body:

{
   "next.dbid":"101",
   "schema.history":"create(5.15)",
   "schema.version":"5.15"
}
Table 15.212. Get engine properties - Response codes
Response code	Description
200	Indicates the properties are returned.


EOF
        ],
        [
            <<<EOF
            Get engine info
EOF
            , 'GET management/engine', 'GET', 'management/engine',
            <<<EOF

Returns a read-only view of the engine that is used in this REST-service.

Success response body:

{
   "name":"default",
   "version":"5.15",
   "resourceUrl":"file://activiti/activiti.cfg.xml",
   "exception":null
}
Table 15.213. Get engine info - Response codes
Response code	Description
200	Indicates the engine info is returned.

Runtime

EOF
        ],
        [
            <<<EOF
            Signal event received
EOF
            , 'POST runtime/signals', 'POST', 'runtime/signals',
            <<<EOF

Notifies the engine that a signal event has been received, not explicitally related to a specific execution.

Body JSON:

{
  "signalName": "My Signal",
  "tenantId" : "execute",
  "async": true,
  "variables": [
      {"name": "testVar", "value": "This is a string"},
      ...
  ]
}
Table 15.214. Signal event received - JSON Body parameters
Parameter	Description	Required
signalName	Name of the signal	Yes
tenantId	ID of the tenant that the signal event should be processed in	No
async	If true, handling of the signal will happen asynchronously. Return code will be 202 - Accepted to indicate the request is accepted but not yet executed. If false, handling the signal will be done immedialty and result (200 - OK) will only return after this completed successfully. Defaults to false if omitted.	No
variables	Array of variables (in the general variables format) to use as payload to pass along with the signal. Cannot be used in case async is set to true, this will result in an error.	No

Success response body:

Table 15.215. Signal event received - Response codes
Response code	Description
200	Indicated signal has been processed and no errors occured.
202	Indicated signal processing is queued as a job, ready to be executed.
400	Signal not processed. The signal name is missing or variables are used toghether with async, which is not allowed. Response body contains additional information about the error.

Jobs

EOF
        ],
        [
            <<<EOF
            Get a single job
EOF
            , 'GET management/jobs/{jobId}', 'GET', 'management/jobs/{jobId}',
            <<<EOF

Table 15.216. Get a single job - URL parameters
Parameter	Required	Value	Description
jobId	Yes	String	The id of the job to get.

Success response body:

{
   "id":"8",
   "url":"http://localhost:8182/management/jobs/8",
   "processInstanceId":"5",
   "processInstanceUrl":"http://localhost:8182/runtime/process-instances/5",
   "processDefinitionId":"timerProcess:1:4",
   "processDefinitionUrl":"http://localhost:8182/repository/process-definitions/timerProcess%3A1%3A4",
   "executionId":"7",
   "executionUrl":"http://localhost:8182/runtime/executions/7",
   "retries":3,
   "exceptionMessage":null,
   "dueDate":"2013-06-04T22:05:05.474+0000",
   "tenantId":null
}
Table 15.217. Get a single job - Response codes
Response code	Description
200	Indicates the job exists and is returned.
404	Indicates the requested job does not exist.


EOF
        ],
        [
            <<<EOF
            Delete a job
EOF
            , 'DELETE management/jobs/{jobId}', 'DELETE', 'management/jobs/{jobId}',
            <<<EOF

Table 15.218. Delete a job - URL parameters
Parameter	Required	Value	Description
jobId	Yes	String	The id of the job to delete.

Table 15.219. Delete a job - Response codes
Response code	Description
204	Indicates the job was found and has been deleted. Response-body is intentionally empty.
404	Indicates the requested job was not found.


EOF
        ],
        [
            <<<EOF
            Execute a single job
EOF
            , 'POST management/jobs/{jobId}', 'POST', 'management/jobs/{jobId}',
            <<<EOF

Body JSON:

{
  "action" : "execute"
}
Table 15.220. Execute a single job - JSON Body parameters
Parameter	Description	Required
action	Action to perform. Only execute is supported.	Yes

Table 15.221. Execute a single job - Response codes
Response code	Description
204	Indicates the job was executed. Response-body is intentionally empty.
404	Indicates the requested job was not found.
500	Indicates the an exception occurred while executing the job. The status-description contains additional detail about the error. The full error-stacktrace can be fetched later on if needed.


EOF
        ],
        [
            <<<EOF
            Get the exception stacktrace for a job
EOF
            , 'GET management/jobs/{jobId}/exception-stacktrace', 'GET', 'management/jobs/{jobId}/exception-stacktrace',
            <<<EOF

Table 15.222. Get the exception stacktrace for a job - URL parameters
Parameter	Description	Required
jobId	Id of the job to get the stacktrace for.	Yes

Table 15.223. Get the exception stacktrace for a job - Response codes
Response code	Description
200	Indicates the requested job was not found and the stacktrace has been returned. The response contains the raw stacktrace and always has a Content-type of text/plain.
404	Indicates the requested job was not found or the job doesn't have an exception stacktrace. Status-description contains additional information about the error.


EOF
        ],
        [
            <<<EOF
            Get a list of jobs
EOF
            , 'GET management/jobs', 'GET', 'management/jobs',
            <<<EOF

Table 15.224. Get a list of jobs - URL query parameters
Parameter	Description	Type
id	Only return job with the given id	String
processInstanceId	Only return jobs part of a process with the given id	String
executionId	Only return jobs part of an execution with the given id	String
processDefinitionId	Only return jobs with the given process definition id	String
withRetriesLeft	If true, only return jobs with retries left. If false, this parameter is ignored.	Boolean
executable	If true, only return jobs which are executable. If false, this parameter is ignored.	Boolean
timersOnly	If true, only return jobs which are timers. If false, this parameter is ignored. Cannot be used together with 'messagesOnly'.	Boolean
messagesOnly	If true, only return jobs which are messages. If false, this parameter is ignored. Cannot be used together with 'timersOnly'	Boolean
withException	If true, only return jobs for which an exception occurred while executing it. If false, this parameter is ignored.	Boolean
dueBefore	Only return jobs which are due to be executed before the given date. Jobs without duedate are never returned using this parameter.	Date
dueAfter	Only return jobs which are due to be executed after the given date. Jobs without duedate are never returned using this parameter.	Date
exceptionMessage	Only return jobs with the given exception message	String
tenantId	No	String	Only return jobs with the given tenantId.
tenantIdLike	No	String	Only return jobs with a tenantId like the given value.
withoutTenantId	No	Boolean	If true, only returns jobs without a tenantId set. If false, the withoutTenantId parameter is ignored.
sort	Field to sort results on, should be one of id, dueDate, executionId, processInstanceId, retries or tenantId.	String
The general paging and sorting query-parameters can be used for this URL.



Success response body:

{
   "data":[
      {
         "id":"13",
         "url":"http://localhost:8182/management/jobs/13",
         "processInstanceId":"5",
         "processInstanceUrl":"http://localhost:8182/runtime/process-instances/5",
         "processDefinitionId":"timerProcess:1:4",
         "processDefinitionUrl":"http://localhost:8182/repository/process-definitions/timerProcess%3A1%3A4",
         "executionId":"12",
         "executionUrl":"http://localhost:8182/runtime/executions/12",
         "retries":0,
         "exceptionMessage":"Can't find scripting engine for 'unexistinglanguage'",
         "dueDate":"2013-06-07T10:00:24.653+0000",
         "tenantId":null
      },

      ...
   ],
   "total":2,
   "start":0,
   "sort":"id",
   "order":"asc",
   "size":2
}
Table 15.225. Get a list of jobs - Response codes
Response code	Description
200	Indicates the requested jobs were returned.
400	Indicates an illegal value has been used in a url query parameter or the both 'messagesOnly' and 'timersOnly' are used as parameters. Status description contains additional details about the error.

Users

EOF
        ],
        [
            <<<EOF
            Get a single user
EOF
            , 'GET identity/users/{userId}', 'GET', 'identity/users/{userId}',
            <<<EOF

Table 15.226. Get a single user - URL parameters
Parameter	Required	Value	Description
userId	Yes	String	The id of the user to get.

Success response body:

{
   "id":"testuser",
   "firstName":"Fred",
   "lastName":"McDonald",
   "url":"http://localhost:8182/identity/users/testuser",
   "email":"no-reply@activiti.org"
}
Table 15.227. Get a single user - Response codes
Response code	Description
200	Indicates the user exists and is returned.
404	Indicates the requested user does not exist.


EOF
        ],
        [
            <<<EOF
            Get a list of users
EOF
            , 'GET identity/users', 'GET', 'identity/users',
            <<<EOF

Table 15.228. Get a list of users - URL query parameters
Parameter	Description	Type
id	Only return user with the given id	String
firstName	Only return users with the given firstname	String
lastName	Only return users with the given lastname	String
email	Only return users with the given email	String
firstNameLike	Only return userswith a firstname like the given value. Use % as wildcard-character.	String
lastNameLike	Only return users with a lastname like the given value. Use % as wildcard-character.	String
emailLike	Only return users with an email like the given value. Use % as wildcard-character.	String
memberOfGroup	Only return users which are a member of the given group.	String
potentialStarter	Only return users which are potential starters for a process-definition with the given id.	String
sort	Field to sort results on, should be one of id, firstName, lastname or email.	String
The general paging and sorting query-parameters can be used for this URL.



Success response body:

{
   "data":[
      {
         "id":"anotherUser",
         "firstName":"Tijs",
         "lastName":"Barrez",
         "url":"http://localhost:8182/identity/users/anotherUser",
         "email":"no-reply@alfresco.org"
      },
      {
         "id":"kermit",
         "firstName":"Kermit",
         "lastName":"the Frog",
         "url":"http://localhost:8182/identity/users/kermit",
         "email":null
      },
      {
         "id":"testuser",
         "firstName":"Fred",
         "lastName":"McDonald",
         "url":"http://localhost:8182/identity/users/testuser",
         "email":"no-reply@activiti.org"
      }
   ],
   "total":3,
   "start":0,
   "sort":"id",
   "order":"asc",
   "size":3
}
Table 15.229. Get a list of users - Response codes
Response code	Description
200	Indicates the requested users were returned.


EOF
        ],
        [
            <<<EOF
            Update a user
EOF
            , 'PUT identity/users/{userId}', 'PUT', 'identity/users/{userId}',
            <<<EOF

Body JSON:

{
  "firstName":"Tijs",
  "lastName":"Barrez",
  "email":"no-reply@alfresco.org",
  "password":"pass123"
}
All request values are optional. For example, you can only include the 'firstName' attribute in the request body JSON-object, only updating the firstName of the user, leaving all other fields unaffected. When an attribute is explicitly included and is set to null, the user-value will be updated to null. Example: {"firstName" : null} will clear the firstName of the user).

Table 15.230. Update a user - Response codes
Response code	Description
200	Indicates the user was updated.
404	Indicates the requested user was not found.
409	Indicates the requested user was updated simultaneously.

Success response body: see response for identity/users/{userId}.


EOF
        ],
        [
            <<<EOF
            Create a user
EOF
            , 'POST identity/users', 'POST', 'identity/users',
            <<<EOF

Body JSON:

{
  "id":"tijs",
  "firstName":"Tijs",
  "lastName":"Barrez",
  "email":"no-reply@alfresco.org",
  "password":"pass123"
}
Table 15.231. Create a user - Response codes
Response code	Description
201	Indicates the user was created.
400	Indicates the id of the user was missing.

Success response body: see response for identity/users/{userId}.


EOF
        ],
        [
            <<<EOF
            Delete a user
EOF
            , 'DELETE identity/users/{userId}', 'DELETE', 'identity/users/{userId}',
            <<<EOF

Table 15.232. Delete a user - URL parameters
Parameter	Required	Value	Description
userId	Yes	String	The id of the user to delete.

Table 15.233. Delete a user - Response codes
Response code	Description
204	Indicates the user was found and has been deleted. Response-body is intentionally empty.
404	Indicates the requested user was not found.


EOF
        ],
        [
            <<<EOF
            Get a user's picture
EOF
            , 'GET identity/users/{userId}/picture', 'GET', 'identity/users/{userId}/picture',
            <<<EOF

Table 15.234. Get a user's picture - URL parameters
Parameter	Required	Value	Description
userId	Yes	String	The id of the user to get the picture for.

Response Body: The response body contains the raw picture data, representing the user's picture. The Content-type of the response corresponds to the mimeType that was set when creating the picture.

Table 15.235. Get a user's picture - Response codes
Response code	Description
200	Indicates the user was found and has a picture, which is returned in the body.
404	Indicates the requested user was not found or the user does not have a profile picture. Status-description contains additional information about the error.


EOF
        ],
        [
            <<<EOF
            Updating a user's picture
EOF
            , 'GET identity/users/{userId}/picture', 'GET', 'identity/users/{userId}/picture',
            <<<EOF

Table 15.236. Updating a user's picture - URL parameters
Parameter	Required	Value	Description
userId	Yes	String	The id of the user to get the picture for.

Request body: The request should be of type multipart/form-data. There should be a single file-part included with the binary value of the picture. On top of that, the following additional form-fields can be present:

mimeType: Optional mime-type for the uploaded picture. If omitted, the default of image/jpeg is used as a mime-type for the picture.
Table 15.237. Updating a user's picture - Response codes
Response code	Description
200	Indicates the user was found and the picture has been updated. The response-body is left empty intentionally.
404	Indicates the requested user was not found.


EOF
        ],
        [
            <<<EOF
            List a user's info
EOF
            , 'PUT identity/users/{userId}/info', 'PUT', 'identity/users/{userId}/info',
            <<<EOF

Table 15.238. List a user's info - URL parameters
Parameter	Required	Value	Description
userId	Yes	String	The id of the user to get the info for.

Response Body:

[
   {
      "key":"key1",
      "url":"http://localhost:8182/identity/users/testuser/info/key1"
   },
   {
      "key":"key2",
      "url":"http://localhost:8182/identity/users/testuser/info/key2"
   }
]
Table 15.239. List a user's info - Response codes
Response code	Description
200	Indicates the user was found and list of info (key and url) is returned.
404	Indicates the requested user was not found.


EOF
        ],
        [
            <<<EOF
            Get a user's info
EOF
            , 'GET identity/users/{userId}/info/{key}', 'GET', 'identity/users/{userId}/info/{key}',
            <<<EOF

Table 15.240. Get a user's info - URL parameters
Parameter	Required	Value	Description
userId	Yes	String	The id of the user to get the info for.
key	Yes	String	The key of the user info to get.

Response Body:

{
   "key":"key1",
   "value":"Value 1",
   "url":"http://localhost:8182/identity/users/testuser/info/key1"
}
Table 15.241. Get a user's info - Response codes
Response code	Description
200	Indicates the user was found and the user has info for the given key..
404	Indicates the requested user was not found or the user doesn't have info for the given key. Status description contains additional information about the error.


EOF
        ],
        [
            <<<EOF
            Update a user's info
EOF
            , 'PUT identity/users/{userId}/info/{key}', 'PUT', 'identity/users/{userId}/info/{key}',
            <<<EOF

Table 15.242. Update a user's info - URL parameters
Parameter	Required	Value	Description
userId	Yes	String	The id of the user to update the info for.
key	Yes	String	The key of the user info to update.

Request Body:

{
   "value":"The updated value"
}
Response Body:

{
   "key":"key1",
   "value":"The updated value",
   "url":"http://localhost:8182/identity/users/testuser/info/key1"
}
Table 15.243. Update a user's info - Response codes
Response code	Description
200	Indicates the user was found and the info has been updated.
400	Indicates the value was missing from the request body.
404	Indicates the requested user was not found or the user doesn't have info for the given key. Status description contains additional information about the error.


EOF
        ],
        [
            <<<EOF
            Create a new user's info entry
EOF
            , 'POST identity/users/{userId}/info', 'POST', 'identity/users/{userId}/info',
            <<<EOF

Table 15.244. Create a new user's info entry - URL parameters
Parameter	Required	Value	Description
userId	Yes	String	The id of the user to create the info for.

Request Body:

{
   "key":"key1",
   "value":"The value"
}
Response Body:

{
   "key":"key1",
   "value":"The value",
   "url":"http://localhost:8182/identity/users/testuser/info/key1"
}
Table 15.245. Create a new user's info entry - Response codes
Response code	Description
201	Indicates the user was found and the info has been created.
400	Indicates the key or value was missing from the request body. Status description contains additional information about the error.
404	Indicates the requested user was not found.
409	Indicates there is already an info-entry with the given key for the user, update the resource instance (PUT).


EOF
        ],
        [
            <<<EOF
            Delete a user's info
EOF
            , 'DELETE identity/users/{userId}/info/{key}', 'DELETE', 'identity/users/{userId}/info/{key}',
            <<<EOF

Table 15.246. Delete a user's info - URL parameters
Parameter	Required	Value	Description
userId	Yes	String	The id of the user to delete the info for.
key	Yes	String	The key of the user info to delete.

Table 15.247. Delete a user's info - Response codes
Response code	Description
204	Indicates the user was found and the info for the given key has been deleted. Response body is left empty intentionally.
404	Indicates the requested user was not found or the user doesn't have info for the given key. Status description contains additional information about the error.

Groups

EOF
        ],
        [
            <<<EOF
            Get a single group
EOF
            , 'GET identity/groups/{groupId}', 'GET', 'identity/groups/{groupId}',
            <<<EOF

Table 15.248. Get a single group - URL parameters
Parameter	Required	Value	Description
groupId	Yes	String	The id of the group to get.

Success response body:

{
   "id":"testgroup",
   "url":"http://localhost:8182/identity/groups/testgroup",
   "name":"Test group",
   "type":"Test type"
}
Table 15.249. Get a single group - Response codes
Response code	Description
200	Indicates the group exists and is returned.
404	Indicates the requested group does not exist.


EOF
        ],
        [
            <<<EOF
            Get a list of groups
EOF
            , 'GET identity/groups', 'GET', 'identity/groups',
            <<<EOF

Table 15.250. Get a list of groups - URL query parameters
Parameter	Description	Type
id	Only return group with the given id	String
name	Only return groups with the given name	String
type	Only return groups with the given type	String
nameLike	Only return groups with a name like the given value. Use % as wildcard-character.	String
member	Only return groups which have a member with the given username.	String
potentialStarter	Only return groups which members are potential starters for a process-definition with the given id.	String
sort	Field to sort results on, should be one of id, name or type.	String
The general paging and sorting query-parameters can be used for this URL.



Success response body:

{
   "data":[
     {
        "id":"testgroup",
        "url":"http://localhost:8182/identity/groups/testgroup",
        "name":"Test group",
        "type":"Test type"
     },

      ...
   ],
   "total":3,
   "start":0,
   "sort":"id",
   "order":"asc",
   "size":3
}
Table 15.251. Get a list of groups - Response codes
Response code	Description
200	Indicates the requested groups were returned.


EOF
        ],
        [
            <<<EOF
            Update a group
EOF
            , 'PUT identity/groups/{groupId}', 'PUT', 'identity/groups/{groupId}',
            <<<EOF

Body JSON:

{
   "name":"Test group",
   "type":"Test type"
}
All request values are optional. For example, you can only include the 'name' attribute in the request body JSON-object, only updating the name of the group, leaving all other fields unaffected. When an attribute is explicitly included and is set to null, the group-value will be updated to null.

Table 15.252. Update a group - Response codes
Response code	Description
200	Indicates the group was updated.
404	Indicates the requested group was not found.
409	Indicates the requested group was updated simultaneously.

Success response body: see response for identity/groups/{groupId}.


EOF
        ],
        [
            <<<EOF
            Create a group
EOF
            , 'POST identity/groups', 'POST', 'identity/groups',
            <<<EOF

Body JSON:

{
   "id":"testgroup",
   "name":"Test group",
   "type":"Test type"
}
Table 15.253. Create a group - Response codes
Response code	Description
201	Indicates the group was created.
400	Indicates the id of the group was missing.

Success response body: see response for identity/groups/{groupId}.


EOF
        ],
        [
            <<<EOF
            Delete a group
EOF
            , 'DELETE identity/groups/{groupId}', 'DELETE', 'identity/groups/{groupId}',
            <<<EOF

Table 15.254. Delete a group - URL parameters
Parameter	Required	Value	Description
groupId	Yes	String	The id of the group to delete.

Table 15.255. Delete a group - Response codes
Response code	Description
204	Indicates the group was found and has been deleted. Response-body is intentionally empty.
404	Indicates the requested group was not found.

Get members in a group
There is no GET allowed on identity/groups/members. Use the identity/users?memberOfGroup=sales URL to get all users that are part of a particular group.


EOF
        ],
        [
            <<<EOF
            Add a member to a group
EOF
            , 'POST identity/groups/{groupId}/members', 'POST', 'identity/groups/{groupId}/members',
            <<<EOF

Table 15.256. Add a member to a group - URL parameters
Parameter	Required	Value	Description
groupId	Yes	String	The id of the group to add a member to.

Body JSON:

{
   "userId":"kermit"
}
Table 15.257. Add a member to a group - Response codes
Response code	Description
201	Indicates the group was found and the member has been added.
404	Indicates the userId was not included in the request body.
404	Indicates the requested group was not found.
409	Indicates the requested user is already a member of the group.

Response Body:

{
   "userId":"kermit",
   "groupId":"sales",
    "url":"http://localhost:8182/identity/groups/sales/members/kermit"
}

EOF
        ]];



        foreach($rawDump as $row) {

            $method = [];

            $methodName = lcfirst( preg_replace('/(\'| |,|\ba\b)/sim', '', ucwords(preg_replace('/[-()]/sim',' ',$row[0]))));

            if($row[2]=='GET' && strpos($methodName,'get')!==0)
                $methodName = 'get'.ucfirst($methodName);

            $method['name'] = $methodName;

            $parameters = ['urlReplace'=>[],'url'=>[], 'json'=>[] ];
            $params = [];
            $responseCodes = [];

            if(preg_match('/URL parameters.+?Description(.+?)

/sim', $row[4], $m)) {
                preg_match_all('/^[\n\r ]*(.+?)[\t ]+(Yes|No)[\t ]+(.+?)[\t ]+(.+?)$/sim', $m[1], $mm, PREG_SET_ORDER);
                foreach($mm as $pm){
                    @$parameters['url'][] = $pm[1];
                    $params[$pm[1]] = [
                        'name' => $pm[1],
                        'required' => $pm[2],
                        'type' => strtolower($pm[3]),
                        'desc' => $pm[4]
                    ];
                }
            }

            if(preg_match('/JSON Body parameters.+?Required(.+?)

/sim', $row[4], $m)) {

                preg_match_all('/^[\n\r ]*([^ ]+?)[\t ]+(.+?)[\t ]+(Yes|No)$/sim', $m[1], $mm, PREG_SET_ORDER);
                foreach($mm as $pm) {
                    @$parameters['json'][] = $pm[1];
                    if(!isset($params[$pm[1]]))
                    $params[$pm[1]] = [
                        'type' => 'mixed',
                        'name' => $pm[1],
                        'required' => $pm[3],
                        'desc' => $pm[2]
                    ];
                }
            }

            if(preg_match('/Response codes.+?Description(.+?)

/sim', $row[4], $m)) {

                preg_match_all('/^[\n\r ]*([0-9]+?)[\t ]+(.+?)$/sim', $m[1], $mm, PREG_SET_ORDER);
                foreach($mm as $pm)
                    @$responseCodes[$pm[1]] = $pm[2];
            }


            if(preg_match('/Request body:[\n\r\t ]*({.+?^}$)/sim', $row[4], $m)) {
                $method['requestBody'] = $m[1];
            }

            if(preg_match_all('/{(.+?)}/sim', $row[3], $m, PREG_SET_ORDER)) {
                foreach($m as $pm) {
                    @$parameters['urlReplace'][] = $pm[1];
                    if(!isset($params[$pm[1]]))
                    $params[$pm[1]] = [
                        'name' => $pm[1],
                        'required' => 'Yes',
                        'type' => 'mixed',
                        'desc' => ''
                    ];
                }
            }

            $method['parameters'] = $parameters;
            $method['params'] = $params;
            $method['responseCodes'] = $responseCodes;
            $method['HTTPVerb'] = $row[2];
            $method['uri'] = $row[3];
            $method['description'] = trim($row[0]);

            echo $this->getMethodBody($method);

        }

    }


    private function getMethodBody($method)
    {
        $methodParams = [];

        $hugeParamList = count($method[ 'params' ]) > 5;

        $ret = "/**\n* {$method['description']}";

        if (@$method[ 'requestBody' ]) {
            $ret .= "\n*\n* request Body example:\n*\n";
            $ret .= preg_replace('/^/sim', '*  ', $method[ 'requestBody' ]);
            $ret .= "\n*\n* @param array \$requestBody";
        }

        if ($hugeParamList) {
            $ret .= "\n*\n* input hash keys:\n*";
            foreach ($method[ 'params' ] as $paramName => $param) {
                if ($param[ 'required' ] == 'No') {
                    $ret .= "\n* " . str_pad($paramName, 30, ' ', STR_PAD_RIGHT) . ": {$param['desc']}";
                }
            }
            $ret .= "\n* @param array \$inputHash";
        }

        foreach ($method[ 'params' ] as $paramName => $param) {
            if (!$hugeParamList || $param[ 'required' ] == 'Yes') {
                $ret .= "\n* @param {$param['type']} \${$paramName} {$param['desc']}";
                $methodParams[ $paramName ] = '$' . $paramName;
            }
        }

        $signatureParams = $methodParams;
        if ($hugeParamList) {
            $signatureParams[ 'inputHash' ] = '$inputHash = []';
        }

        if (@$method[ 'requestBody' ]) {
            $signatureParams[ 'requestBody' ] = '$requestBody = ""';
        }


        if (count(@$method[ 'responseCodes' ]) > 1) {
            $ret .= "\n\n* @throws \\Exception";
        }

        $ret .= "\n\n* @return array\n**/\n";
        $ret .= "public function {$method['name']}(" . implode(', ', $signatureParams) . ") {\n";

        if (!@$method[ 'requestBody' ]) {
            $ret .= "\$requestBody = null;\n";
        }

        $k = array_keys($methodParams);
        $ark = [];
        foreach ($k as $ak) {
            $ark[] = "'$ak' => \$$ak";
        }

        if ($hugeParamList) {
            $ret .= "\$inputArray = array_merge(\$inputHash, [" . implode(', ', $ark) . "]);\n";
        } else {
            $ret .= "\$inputArray = [" . implode(', ', $ark) . "];\n";
        }

        $ret .= "\$ret = \$this->fetch('{$method['HTTPVerb']}', '{$method['uri']}', \$requestBody, \$inputArray, " . var_export(
                @$method[ 'parameters' ][ 'url' ],
                1
            ) . ", " . var_export(@$method[ 'parameters' ][ 'json' ], 1) . ", " . var_export(
                @$method[ 'responseCodes' ],
                1
            ) . ");\n";


        $ret .= " return \$ret;\n}\n\n";

        return $ret;
    }
}