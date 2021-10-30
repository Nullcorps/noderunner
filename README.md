# noderunner

This is a wordpress plugin which uses a separate linking system. Honestly it's not that different from normal menus/links other than that it shows the nodes which link TO a given page as well as FROM it. That changes the way you traverse the space somehow and I still can't figure out if it's a good idea or just perception but I'm writing this wordpress version of it to find out.

a "node" = a page or post. perhaps other types of data too in future


How is this different from normal menus or tags/categories?
-----------------------------------------------------------------------

two things:

- making linking to other content within the site really easy i.e. you do it in page, don't have to navigate away to add links or whatever. This makes it quite quick to organise or map out a large bunch of content/nodes.

- forward as well as backard navigation/links. i.e. from any any page/node you can see not only the links *from* that page (as you'd expect) but also the links *to* that page. So you can sorta navigate "upstream" on a subject.

The links are not intended to replace inline links or whatever, think of them more like topics or chapter links.

This system lends itself to organising lots of scraps of somewhat related data, like a manual or directory, or for recipes/potions/spells/musings/poetry.. stuff that may not fit very well into a tree type menu structure. So if you were writing about a subject, but weren't quite sure exactly how you wanted to connect the sub-topics, you can just start writing and get it down on the page and then just link that content to other pages/nodes as you go along and then as it fills out you might find a structure does actually emerge. or not. idk. your focus is on the words and how the topics relate to each other rather than how do I organise all these topics into any sort of hierarchical menu system whilst maintaqining creative flow.. 

With Noderunner from a visitor's point of view you can basically drop in at any page/post(node) nd there then doesn't need to be a tree-structure menu. It can be a completely fluid thing (but you can also have a few basic links at the top (home/help/contact etc) or your tree structure.. or both. idk. I just make it, use it how you like.



To make it work
----------------------------------------------------------------------------

download or clone the code into: /wp-content/plugins/ 

so then it would be like: /wp-content/plugins/noderunner/

you should see it in the plugin list, activate it.

To use it you need to put the links somewhere. These can be in a sidebar (e.g. they'd be the main page links) or perhaps in a widget/footer as a "you may also be intersted in..." type thing to catch a user about to leave.

To put the links add the following shortcodes to a sidebar, footer widget or wherever you want to use them:

**[noderunner_links_from_here]**

**[noderunner_links_to_here]**

**[noderunner_create_a_link]**

Or if you switch to the code editor in wordpress you can put all 3 in a single block and then use a plugin like widget contex to hide the noderunner links on key pages (e.g home page, cart/checkout etc) by url filter (or whatever you choose):

Then you can us the create_a_link widget to make a link from any page/post to any other page/post, and those links will be able to be followed in both directions... so if you set up a 'home' page and a 'topic1' page, you only need to make a link from home to topic1 since the link back to home (when on the topic1 page) will be there automatically as an "upstream" link.

Rinse and repeat.




But isn't this literally tags/categories/taxonimies etc?
--------------------------------------------------------------------

Actually no. tags/categories aggregate results. so you search on /tag/bibbedy and it shows you all posts/pages matching content with the bibbedy tag. This is not that. 



TODO LIST:
------------------------------------------------------------------------

- make it so that you can not only create a link "in page" but also create a new page. So then you could start off with just a home page and map out an entire site really quickly without having to navigate away from your current screen. Fill in subject and brief body data right there in the widget and can then navigate to that new page from your current page (or not if just mapping out a structure).

IN PROG - remove any inline css

DONE! - add deleting links too
   - it's not pretty but it works

