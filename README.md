# balafon module [igk/bviewParser]
@C.A.D. BONDJE DOUE
allow to use .bview file as source of loading project's view. 


## how to use?
- create in a project's view folder a view file that will have ".bview" extension 
- write document with "bview language syntaxe"


## bview language syntax

```json
div.main > section > nav{
    ul{
        li.list-item#first{
            - first item 
        }
        /** define class and id */
        li.item-item#second{
            - second item
            /* active attribute */
            @active
            /* set node attribute with array */
            transform:[
               /* here selection not allowed */ 
            ]
            /* set node attribute with nil|null */ 
            filter: nil

        } 
        li.item{
            input{
                type:text
                /* activate muliple attribute */
                @disabled @readonly
            }
        }
        li.item{
            /* conditional node */
            *if: {{ $raw->active }}
            - this item is active
            
        }
        li.item{
            /* build with json data*/
            transform: json({
                "info"=>"true",
                "litteral"=>"ok"
            }),
            /* load bhtml litteral */
            - bhtml(<div> loading some data directly</div>)

            /* with html */
            - html(<div></div>)
            /* with xml */
            - xml(<svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512"><path d="M336 176h40a40 40 0 0140 40v208a40 40 0 01-40 40H136a40 40 0 01-40-40V216a40 40 0 0140-40h40" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32"/><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32" d="M176 272l80 80 80-80M256 48v288"/></svg>)
        }
    }
}
```




### 

### release 
- 1.0

