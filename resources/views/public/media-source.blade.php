<div class="py-4">
	<h4 class="text-2xl font-bold mb-4">Jellyfin Settings</h4>

	<form action="/dmp-jelllyfin/settings" method="post">
		@csrf
		@method('put')
		<div class="mb-5">
			<label for="jelllyfin-server-url" class="block mb-2 font-bold"
				>Jellyfin IP Address</label
			>
			<input
				type="text"
				class="w-full mb-2"
				id="jelllyfin-server-url"
				aria-describedby="jelllyfin-server-urlHelp"
				name="jelllyfin_ip_address"
				value="{{ $options['jelllyfin_ip_address'] }}"
				required
			/>
			<div id="jelllyfin-server-urlHelp" class="text-gray-400 text-sm">Ex: localhost, 10.0.0.32</div>
		</div>

		<div class="mb-5">
			<label for="jelllyfin-server-port" class="block mb-2 font-bold"
				>Jellyfin API Token</label
			>
			<input
				type="text"
				class="w-full mb-2"
				id="jelllyfin-server-port"
				aria-describedby="jelllyfin-server-portHelp"
				name="jelllyfin_token"
				value="{{ $options['jelllyfin_token'] }}"
				required
			/>
			<div id="jelllyfin-server-portHelp" class="text-gray-400 text-sm"></div>
		</div>

		<div class="mb-5">
			<label for="jellyfin-use-ssl" class="text-gray-300 block mb-2 font-bold flex items-center">
				<input
					type="checkbox"
					class="text-black"
					id="jellyfin-use-ssl"
					aria-describedby="plex-use-sslHelp"
					name="jellyfin_use_ssl"
					value="1"
					@checked(old('jellyfin_use_ssl', $options['jelllyfin_use_ssl']))
				/>
				<span class="ml-2">Use SSL for Jellyfin IP address</span>
			</label>
			<div id="plex-use-sslHelp" class="text-gray-400 text-sm"></div>
		</div>

		<button type="submit" class="btn text-black bg-gray-300 text-md px-3 py-1 rounded-sm hover:bg-gray-100">Save Jellyfin Settings</button>
	</form>
</div>
