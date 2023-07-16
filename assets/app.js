import './bootstrap';
import {registerReactControllerComponents} from '@symfony/ux-react';

registerReactControllerComponents(require.context('./react/controllers', true, /\.jsx?$/));