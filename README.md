# wordpress-debugbar...

![Image of scrutinizer](https://scrutinizer-ci.com/g/dennie170/wordpress-debugbar/badges/quality-score.png?b=master)

Is a debugbar based on the famous Debugbar from Maximebf.


## Usage

To use this plugin, simply download a zip of this repo, and install it through Wordpress or copy the files into a folder in the plugins folder. 

### Usage in CutlassWP Theme
To use this properly in the CutlassWP theme, you have to edit a certain file.
Add the following line you thePathOfYourTheme/inc/blade/application/models/main-model.php as the first line of the template_include_blade function: do_action('template_include_blade', $template);

This allows the debugbar to catch the real template name instead of the cached filename.
