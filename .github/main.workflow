# Workflow to publish plugin release to wordpress.org
workflow "Release Plugin" {
  on = "push"
  resolves = ["wordpress"]
}

# Filter for tag
action "tag" {
  uses = "actions/bin/filter@master"
  args = "tag"
}

# Install Dependencies
action "install" {
  uses = "actions/npm@master"
  needs = "tag"
  args = "install"
}

# Build Plugin
action "build" {
  uses = "actions/npm@master"
  needs = ["install"]
  args = "run build"
}

# Create Release ZIP archive
action "archive" {
  uses = "lubusIN/actions/archive@master"
  needs = ["build"]
  env = {
    ZIP_FILENAME = "wp-simple-mail-sender"
  }
}

# Publish to wordpress.org repository
action "wordpress" {
  uses = "lubusIN/actions/wordpress@master"
  needs = ["archive"]
  env = {
    WP_SLUG = "wp-simple-mail-sender"
  }
  secrets = [
    "WP_USERNAME",
    "WP_PASSWORD",
  ]
}
