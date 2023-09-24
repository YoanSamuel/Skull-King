// register any custom, 3rd party controllers here
// app.register('some_controller_name', SomeImportedController);
// assets/bootstrap.js

// Registers Stimulus controllers from controllers.json and in the controllers/ directory
import {startStimulusApp} from "@symfony/stimulus-bridge";

export const app = startStimulusApp(require.context(
    '@symfony/stimulus-bridge/lazy-controller-loader!./controllers',
    true,
    /\.tsx?$/
));