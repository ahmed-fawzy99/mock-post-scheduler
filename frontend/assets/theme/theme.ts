import {definePreset} from '@primeuix/themes';
import Aura from '@primeuix/themes/aura';

const MyPreset = definePreset(Aura, {
    semantic: {
        primary: {
            50: '{purple.50}',
            100: '{purple.100}',
            200: '{purple.200}',
            300: '{purple.300}',
            400: '{purple.400}',
            500: '{purple.500}',
            600: '{purple.600}',
            700: '{purple.700}',
            800: '{purple.800}',
            900: '{purple.900}',
            950: '{purple.950}'
        },
        colorScheme: {
            light: {
                primary: {
                    color: '{purple.500}',
                    inverseColor: '{purple.900}',
                    hoverColor: '{purple.700}',
                    activeColor: '{purple.600}'
                },
            },
            dark: {
                primary: {
                    color: '{purple.500}',
                    inverseColor: '{purple.600}',
                    hoverColor: '{purple.700}',
                    activeColor: '{purple.600}'
                },
            }
        }
    }
});


export default {
    preset: MyPreset,
    options: {
        darkModeSelector: '.dark',
    }
};