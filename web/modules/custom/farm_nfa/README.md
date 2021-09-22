# To reinstall from scratch with all modules needed

```
./vendor/bin/drush site-install farm farm.modules='["farm_land", "farm_activity", "farm_observation", "farm_input", "farm_harvest", "farm_quantity_standard", "farm_role_roles", "farm_api", "farm_dashboard", "farm_login", "farm_ui", "farm_nfa", "farm_migrate","farm_map_mapbox"]'
```

# Migration

See https://gist.github.com/spalladino/6d981f7b33f6e0afe6bb on how to load a db from outside a docker container.

Migration documentation: https://docs.farmos.org/hosting/migration/

From the nfa_php container:

```
./vendor/bin/drush farm_migrate:import
```

# Get farm_forest

```
git clone https://github.com/farmOS/farm_forest.git (into www/web/modules)
git checkout 2.x
```

# Update from farm_nfa repo
```
composer update farmos/farmos --with-dependencies
```
