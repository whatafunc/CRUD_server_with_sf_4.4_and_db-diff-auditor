# How a New Addon Works in X-Cart

When you add a new addon to your X-Cart project, it's a two-step process: first, you place the addon files in the correct directory structure, and second, you run a command to make the system recognize and install the addon.

### 1. Addon Structure and `main.yaml`

The system discovers new addons by scanning the `/modules` directory. For your addon to be recognized, it must follow a specific structure:

*   **Directory:** `/modules/<Author>/<Name>/`
    *   `<Author>`: The author or vendor of the module (e.g., `CDev`).
    *   `<Name>`: The name of the module (e.g., `Coupons`).
*   **`main.yaml` file:** Inside the module's directory, you must have a `config/main.yaml` file. This file is crucial as it contains all the metadata about your addon.

Here is an example of what a `main.yaml` file might look like:

```yaml
type: common
authorName: 'Your Name'
moduleName: 'My Awesome Addon'
description: 'This is a description of my awesome addon.'
version: '5.5.0.0'
dependsOn:
  - CDev-Core
incompatibleWith: []
showSettingsForm: true
canDisable: true
autoloader:
  - 'classes'
```

*   The system reads this file to learn about your addon, including its name, description, version, and dependencies.
*   The `autoloader` key tells X-Cart where to find the classes for your module.

Once you have your addon's files in the correct location with a valid `main.yaml`, you need to tell X-Cart to "rebuild" itself.

### 2. The Rebuild Process

Simply adding the files is not enough. You need to run the rebuild command to make the system aware of your new addon. This is done using the `service-tool`'s `rebuild` command. Although I cannot run it in this environment due to a PHP version mismatch, the command would look like this:

```bash
php bin/service xcst:rebuild --install=<Author>-<Name>
```

Here’s what happens when you run this command:

1.  **Registration**:
    *   The system reads the `main.yaml` file of the module you specified.
    *   It takes the metadata from this file and saves it to the database in the `module` table. This is how the addon gets officially registered.

2.  **Cache Generation**:
    *   After the addon is registered in the database, the rebuild process regenerates several cache and configuration files in the `config/dynamic/` directory.
    *   The most important of these is `config/dynamic/xcart_modules.yaml`. This file is a cached representation of all modules in the system, and it's what the application uses at runtime to know which modules are available and enabled.
    *   If your addon includes a Symfony bundle, `config/dynamic/xcart_bundles.php` is also updated to include your bundle.

3.  **Activation**:
    *   With the caches updated, your addon is now "live". The application will load its code, and it will appear in the list of installed addons in the admin area.

In summary, when you add a new addon, you first provide the files and metadata, and then you run the rebuild command to make the system register your addon and update its caches. After that, your addon becomes an integrated part of the application.

---

# Modular Development in Symfony: Reusing the "Module Level" Approach

The approach you've seen in X-Cart can indeed be replicated in a standard Symfony skeleton, and in fact, it aligns perfectly with Symfony's core design principles.

### The Symfony Equivalent: Bundles

In X-Cart, they call them "addons" or "modules." In the Symfony world, the direct equivalent is a **Bundle**. A Bundle is like a plugin for your application. It can have its own controllers, services, database entities, configuration, and templates—everything it needs to provide a specific feature, completely self-contained.

The key idea is to move from a single, monolithic `/src` directory to a structure where features are grouped into independent bundles.

### How to Implement a Modular Approach in a Symfony Skeleton

The X-Cart implementation is highly customized with a database-driven module registry and dynamic config files. In a standard Symfony project, the process is much simpler and relies on built-in features.

Here are the steps to create a modular architecture:

#### 1. Create a Directory for Your Modules

It's a good convention to create a directory to house your custom bundles. While you could place them in `/src`, putting them in a dedicated directory like `/modules` makes the separation clearer.

```
my-symfony-project/
├── config/
├── modules/      <-- Your custom bundles will live here
├── public/
├── src/          <-- For core application logic
├── var/
└── vendor/
```

#### 2. Create a Custom Bundle

Symfony provides a generator to create a new bundle for you. From your project root, you would run:

```bash
php bin/console make:bundle
```

The generator will ask you for a name (e.g., `BlogBundle`) and create a directory structure for you, typically inside `/src`. You can then move this generated bundle directory into `/modules`.

A bundle directory looks like this:

```
/modules/BlogBundle/
├── BlogBundle.php         # The main bundle class
├── Controller/
├── DependencyInjection/
├── Entity/
├── Repository/
└── templates/
```

#### 3. Register Your Bundle's Autoloader

Next, you need to tell Composer how to find your bundle's classes. You do this by adding a new PSR-4 entry in your `composer.json` file.

```json
{
    "autoload": {
        "psr-4": {
            "App\\": "src/",
            "BlogBundle\\": "modules/BlogBundle/"
        }
    },
    ...
}
```

After adding this, you must run `composer dump-autoload` to update the autoloader.

#### 4. Enable the Bundle

To activate your bundle, you simply add it to the `config/bundles.php` file. This file tells the Symfony kernel which bundles to load.

```php
// config/bundles.php
return [
    Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],
    // ... other bundles
    BlogBundle\BlogBundle::class => ['all' => true], // Add your bundle here
];
```

#### 5. Import Bundle Configuration and Routing

Your bundle can have its own routing and service configuration. You just need to import them from your main application's configuration.

For example, to load routes from your bundle, you would add this to `config/routes.yaml`:

```yaml
# config/routes.yaml
blog_bundle:
    resource: '@BlogBundle/Controller/'
    type: annotation
```

### Summary: X-Cart vs. Standard Symfony

*   **Discovery:** X-Cart uses a custom `rebuild` command and a database to discover and register modules. In standard Symfony, you manually register bundles in `config/bundles.php`.
*   **Complexity:** The standard Symfony approach is much simpler and requires no custom tooling. You are using the framework as it was designed.
*   **Flexibility:** Both approaches give you the same powerful result: a decoupled, modular application where functionality is encapsulated within independent units.

By using bundles, you can absolutely build your Symfony application on a module level, keeping your `/src` directory for only the most core, application-wide logic. This is the recommended approach for any large or complex Symfony project.