# Workflow to publish plugin release to wordpress.org
workflow "Release Plugin" {
  resolves = ["wordpress"]
  on = "release"
}

# Filter for tag
action "tag" {
  uses = "actions/bin/filter@master"
  args = "tag"
}

# Install Dependencies

# Build Plugin

# Create Release ZIP archive
action "archive" {
  uses = "lubusIN/actions/archive@master"
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
