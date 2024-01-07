## Photobomber implementation notes

[comment]: <> by reading the requirements some implementation details on how the UI should look like were my most large struggle, as it was holidays I didn't want to bother the team.
In a real world situation I would ask the Product Manager and validate some mockups.

I took the liberty of design two main pages: photo gallery and album gallery.
Photo Gallery is the place for uploading photos and manage them by adding to an album.
Album Gallery is where I list all albums. It can go down to the album details section, where I would compile album, change info, etc.

For the frontend, a few considerations:
- Not very familiar to tailwind css
- I tried to make reusable components as much as I could
- I created componets for forms, in case i need to reuse
- I reuse the photo gallery component in two different contexts: see all photos and also to add photos to a specific album. For that I am relying on a prop, albumId
- I didnt use Breeze for forms, just to save some time


For the backend

- I had to create migrations to add new columns to the album table, adding layout (used in the interface) and also status (Used for album compilation)
- To save some time, I didn't use Invoke method as I am not too familiar, instead I took a more traditional approach (also, just to save some time)
- There's space for unit test improvements and assetions 


Future steps
- I would add the js methods to a separated js file, just for organization and code reading purposes
- Same for CSS changes, removing it from the components
- use vuex (asynchronous operations and separation of concerns)
- use jest for frontend tests
- adding toast messages to inform user of success or fail
- better error handling
- css aligments tweaks